<?php

namespace v3knet\module\system\traits;

use Silex\Application;
use v3knet\module\Module;

trait ModularAppTrait
{

    use V3kAppTrait;

    private $modules = [];

    /**
     * @TODO Fire module.register event.
     * @param string|Module      $module
     * @param string|Module|null $instance
     * @return self
     */
    public function registerModule($module, $instance = null)
    {
        if ($module instanceof Module) {
            $name = $module->getMachineName();
            $instance = $module;
        }
        else {
            if (!is_string($module) || is_null($instance)) {
                throw new \UnexpectedValueException();
            }

            $name = $module;
            $instance = is_string($instance) ? new $instance : $instance;
        }

        return $this->doRegisterModule($name, $instance);
    }

    private function doRegisterModule($name, Module $instance)
    {
        if (!$this->isModuleExists($name)) {
            foreach ($instance->getRequires() as $require) {
                $this->registerModule(new $require);
            }

            $index = count($this->providers);
            $this->modules[$name] = $index;
            $this->register($instance);
            $this->mount($instance->getRoutePrefix(), $instance);
        }

        return $this;
    }

    protected function bootModules()
    {
        foreach ($this->modules as $name => $index) {
            if (is_string($this->modules[$name])) {
                $this->modules[$name] = new $this->modules[$name];
            }
        }
    }

    /**
     * @return string[]
     */
    public function getModules()
    {
        return array_keys($this->modules);
    }

    /**
     * @param string $name
     * @return Module
     */
    public function getModule($name)
    {
        $index = $this->modules[$name];

        return $this->providers[$index];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isModuleExists($name)
    {
        return isset($this->modules[$name]);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getModulePath($name)
    {
        return $this->getModule($name)->getPath();
    }

    public function getModuleNamespace($name)
    {
        $class = get_class($this->getModule($name));

        return substr($class, 0, strrpos($class, '\\'));
    }

    /**
     * Convert the magic service name to class name.
     * For example: @system.ctrl.home -> MODULE_NAMESPACE\controllers\HomeController
     *
     * @see Module::getMagicServiceClass()
     */
    public function getMagicServiceClass($name)
    {
        if (0 !== strpos($name, '@')) {
            throw new \RuntimeException(sprintf('%s is not magic service', $name));
        }

        list($module, $group, $chunks) = explode('.', $name, 3);
        $module = trim($module, '@');

        return $this->getModule($module)->getMagicServiceClass($group, $chunks);
    }

}
