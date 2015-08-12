<?php

namespace atsilex\module\system;

use atsilex\module\system\traits\GetterAppTrait;
use atsilex\module\system\traits\ModularAppTrait;
use Composer\Autoload\ClassLoader;
use Silex\Application;

class ModularApp extends Application
{

    const VERSION = '1.0-dev';

    use ModularAppTrait;
    use GetterAppTrait;

    public function __construct(array $values = [], ClassLoader $loader = null)
    {
        parent::__construct($values);

        if (!$this->offsetExists('app.root')) {
            throw new \InvalidArgumentException('Missing app.root value.');
        }

        is_null($loader) && $this->setClassLoader($loader);

        $this->before([$this, 'onBefore']);
        $this->error([$this, 'onError']);

        // Register configured modules
        if (!empty($this['modules'])) {
            foreach ($this['modules'] as $name => $module) {
                $this->registerModule($name, $module);
            }
        }
    }

}
