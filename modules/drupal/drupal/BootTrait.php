<?php

namespace atphp\module\drupal\drupal;

use Symfony\Component\EventDispatcher\GenericEvent;

trait BootTrait
{

    /** @var bool */
    private $booted = false;

    public function isBooted()
    {
        return $this->booted;
    }

    public function boot()
    {
        if (!$this->isBooted() && $this->booted = true) {
            define('DRUPAL_ROOT', $this->root);
            require_once DRUPAL_ROOT . '/includes/bootstrap.inc';

            $this->doBoot();
        }

        return $this;
    }

    private function doBoot()
    {
        $dir = &drupal_static('conf_path', 'sites/');
        $dir = $this->siteDir;

        if ($this->global) {
            foreach ($this->global as $k => $v) {
                $GLOBALS[$k] = $v;
            }
        }

        drilex_dispatcher()
            ->addListener('drupal.boot.post', function (GenericEvent $event) {
                global $conf;

                switch ($event['currentPhase']) {
                    case DRUPAL_BOOTSTRAP_VARIABLES:
                        foreach ($this->conf as $k => $v) {
                            $conf[$k] = $v;
                        }
                        break;
                }
            });

        $this->doBootPhases(DRUPAL_BOOTSTRAP_FULL);

        $implements = &drupal_static('module_implements');
        foreach (explode(',', DRILEX_HOOK_IMPLEMENTATION) as $fn) {

            list($module, $hook) = explode('_', $fn, 2);
            $implements[$hook][$module] = false;
        }
    }

    protected function doBootPhases($phase = null, $new_phase = true)
    {
        static $finalPhase;
        static $storedPhase = -1;
        static $phases = [
            DRUPAL_BOOTSTRAP_CONFIGURATION,
            DRUPAL_BOOTSTRAP_PAGE_CACHE,
            DRUPAL_BOOTSTRAP_DATABASE,
            DRUPAL_BOOTSTRAP_VARIABLES,
            DRUPAL_BOOTSTRAP_SESSION,
            DRUPAL_BOOTSTRAP_PAGE_HEADER,
            DRUPAL_BOOTSTRAP_LANGUAGE,
            DRUPAL_BOOTSTRAP_FULL,
        ];

        if (isset($phase)) {
            if ($new_phase && $phase >= $storedPhase) {
                $finalPhase = $phase;
            }

            while ($phases && $phase > $storedPhase && $finalPhase > $storedPhase) {
                $currentPhase = array_shift($phases);
                $storedPhase = $currentPhase > $storedPhase ? $currentPhase : $storedPhase;
                $params = ['storedPhase' => $storedPhase, 'currentPhase' => $currentPhase];

                drilex_dispatcher()->dispatch('drupal.boot.pre', drilex_event($params));
                $this->doBootPhase($currentPhase);
                drilex_dispatcher()->dispatch('drupal.boot.post', drilex_event($params));
            }
        }

        return $storedPhase;
    }

    private function doBootPhase($currentPhase)
    {
        switch ($currentPhase) {
            case DRUPAL_BOOTSTRAP_CONFIGURATION:
                return _drupal_bootstrap_configuration();

            case DRUPAL_BOOTSTRAP_PAGE_CACHE:
                return _drupal_bootstrap_page_cache();

            case DRUPAL_BOOTSTRAP_DATABASE:
                return _drupal_bootstrap_database();

            case DRUPAL_BOOTSTRAP_VARIABLES:
                return _drupal_bootstrap_variables();

            case DRUPAL_BOOTSTRAP_SESSION:
                require_once DRUPAL_ROOT . '/' . variable_get('session_inc', 'includes/session.inc');
                return drupal_session_initialize();

            case DRUPAL_BOOTSTRAP_PAGE_HEADER:
                return _drupal_bootstrap_page_header();

            case DRUPAL_BOOTSTRAP_LANGUAGE:
                return drupal_language_initialize();

            case DRUPAL_BOOTSTRAP_FULL:
                return $this->doBootFull();
        }
    }

    private function doBootFull()
    {
        require_once DRUPAL_ROOT . '/includes/common.inc';
        require_once DRUPAL_ROOT . '/' . variable_get('path_inc', 'includes/path.inc');
        require_once DRUPAL_ROOT . '/includes/theme.inc';
        require_once DRUPAL_ROOT . '/includes/pager.inc';
        require_once DRUPAL_ROOT . '/' . variable_get('menu_inc', 'includes/menu.inc');
        require_once DRUPAL_ROOT . '/includes/tablesort.inc';
        require_once DRUPAL_ROOT . '/includes/file.inc';
        require_once DRUPAL_ROOT . '/includes/unicode.inc';
        require_once DRUPAL_ROOT . '/includes/image.inc';
        require_once DRUPAL_ROOT . '/includes/form.inc';
        require_once DRUPAL_ROOT . '/includes/mail.inc';
        require_once DRUPAL_ROOT . '/includes/actions.inc';
        require_once DRUPAL_ROOT . '/includes/ajax.inc';
        require_once DRUPAL_ROOT . '/includes/token.inc';
        require_once DRUPAL_ROOT . '/includes/errors.inc';

        unicode_check();
        fix_gpc_magic();
        module_load_all();
        file_get_stream_wrappers();
        $seed = unpack("L", drupal_random_bytes(4));
        mt_srand($seed[1]);

        $test_info = &$GLOBALS['drupal_test_info'];
        if (!empty($test_info['in_child_site'])) {
            ini_set('log_errors', 1);
            ini_set('error_log', 'public://error.log');
        }

        drupal_path_initialize();

        if (!defined('MAINTENANCE_MODE') || MAINTENANCE_MODE != 'update') {
            menu_set_custom_theme();
            $this->initializeTheme();
            module_invoke_all('init');
        }
    }

    private function initializeTheme()
    {
        global $theme, $user, $theme_key;

        // If $theme is already set, assume the others are set, too, and do nothing
        if (isset($theme)) {
            return;
        }

        $themes = list_themes();
        $theme = !empty($user->theme) && drupal_theme_access($user->theme) ? $user->theme : variable_get('theme_default', 'bartik');

        $custom_theme = menu_get_custom_theme();
        $theme = !empty($custom_theme) ? $custom_theme : $theme;

        // Store the identifier for retrieving theme settings with.
        $theme_key = $theme;

        // Find all our ancestor themes and put them in an array.
        $base_theme = array();
        $ancestor = $theme;
        while ($ancestor && isset($themes[$ancestor]->base_theme)) {
            $ancestor = $themes[$ancestor]->base_theme;
            $base_theme[] = $themes[$ancestor];
        }
        _drupal_theme_initialize($themes[$theme], array_reverse($base_theme));

        // Themes can have alter functions, so reset the drupal_alter() cache.
        drupal_static_reset('drupal_alter');

        $setting['ajaxPageState'] = array(
            'theme'       => $theme_key,
            'theme_token' => drupal_get_token($theme_key),
        );
        drupal_add_js($setting, 'setting');
    }

}
