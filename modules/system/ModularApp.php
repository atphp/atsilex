<?php

namespace atsilex\module\system;

use atsilex\module\system\traits\GetterAppTrait;
use atsilex\module\system\traits\ModularAppTrait;
use Composer\Autoload\ClassLoader;
use Silex\Application;
use Silex\Application\SecurityTrait;
use Silex\Application\UrlGeneratorTrait;
use Silex\Provider\CsrfServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\VarDumperServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;

class ModularApp extends Application
{

    const VERSION = '0.1.1-dev';

    use ModularAppTrait;
    use GetterAppTrait;
    use UrlGeneratorTrait;
    use SecurityTrait;

    protected $requiredConfigKeys = ['app.root'];

    public function __construct(array $values = [], ClassLoader $loader = null)
    {
        parent::__construct($values);

        $this['app'] = $this;
        
        foreach ($this->requiredConfigKeys as $k) {
            if (!$this->offsetExists($k)) {
                throw new \InvalidArgumentException(sprintf('Missing "%s" value.', $k));
            }
        }

        $this->setClassLoader($loader);

        $this->before([$this, 'onBefore']);
        $this->error([$this, 'onError']);

        // Register configured modules
        if (!empty($this['modules'])) {
            foreach ($this['modules'] as $name => $module) {
                $this->registerModule($name, $module);
            }
        }
    }

    public function boot()
    {
        $this->register(new CsrfServiceProvider());
        $this->register(new FormServiceProvider());
        $this->register(new HttpFragmentServiceProvider());
        $this->register(new LocaleServiceProvider());
        $this->register(new SecurityServiceProvider(), ['security.firewalls' => $this['security.firewalls']]);
        $this->register(new ServiceControllerServiceProvider());
        $this->register(new TranslationServiceProvider());
        $this->register(new ValidatorServiceProvider());

        if (isset($this['debug']) && !empty($this['debug'])) {
            $this->register(new VarDumperServiceProvider());

            if (class_exists(WebProfilerServiceProvider::class)) {
                $this->register(new WebProfilerServiceProvider(), [
                    'profiler.cache_dir' => $this['app.root'] . '/files/profiler',
                ]);
            }
        }

        if (!$this->isModuleExists('system')) {
            $this->registerModule(new SystemModule());
        }

        return parent::boot();
    }

}
