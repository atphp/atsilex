<?php

namespace atsilex\module\system;

use atsilex\module\dev\DevModule;
use atsilex\module\Module;
use atsilex\module\system\providers\Register;
use Pimple\Container;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * @TODO
 *
 *  Features
 *  ---------------------
 *  - Include Swift Mailer.
 *  - Hal
 *      - https://github.com/mikekelly/hal-browser
 *      - Nocarrier\Hal — https://github.com/blongden/hal
 *      - https://github.com/easybiblabs/silex-hal-provider
 *      - http://www.slideshare.net/LukeStokes/pox-to-hateoas-13077649
 *  - Support drupal navbar and/or github.com/vadikom/smartmenus
 *  - Document how to change cache backend.
 */
class SystemModule extends Module
{
    protected $machineName = 'system';
    protected $name        = 'System Module';
    protected $description = 'Implements core functions.';
    protected $routeFile   = true;

    /**
     * Created when Twig environment object is created.
     */
    const EVENT_TWIG_CREATE = 'system.twig.create';

    /**
     * On application install.
     */
    const EVENT_APP_INSTALL = 'system.app.install';

    /**
     * @param ModularApp $c
     */
    public function register(Container $c)
    {
        if ('cli' === php_sapi_name()) {
            if (!$c->isModuleExists('dev')) {
                $c->registerModule(new DevModule());
            }
        }

        (new Register())->register($c);
    }

    public function boot(Application $app)
    {
        if ($app->has('site_frontpage') && ('/' !== $app->get('site_frontpage'))) {
            $app->get('/', function (Container $c) {
                return $c->handle(Request::create($c->get('site_frontpage')));
            });
        }

        parent::boot($app);
    }
}
