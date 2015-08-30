<?php

namespace atsilex\module\system\traits;

use atsilex\module\system\providers\container\AppAwareContainer;
use atsilex\module\system\providers\container\ContainerBuilder;

trait ContainerAppTrait
{

    private $container;
    private $containerNs    = 'atsilex';
    private $containerClass = 'atcontainer';

    public function getCachedContainerFilePath()
    {
        return $this->getAppRoot() . '/files/container.php';
    }

    public function deleteCachedContainerFile()
    {
        $file = $this->getCachedContainerFilePath();
        if (is_file($file)) {
            unlink($file);
        }
    }

    /**
     * @param bool $rebuild
     * @return AppAwareContainer
     */
    public function getContainer($rebuild = false)
    {
        if ($rebuild || (null === $this->container)) {
            $file = $this->getAppRoot() . '/files/container.php';

            if (!is_file($file)) {
                $builder = new ContainerBuilder($this);
                $builder->build($file, $this->containerNs, $this->containerClass);
            }

            require_once $file;
            $class = "{$this->containerNs}\\{$this->containerClass}";
            $this->container = new $class;
            $this->container->setApp($this);
        }

        return $this->container;
    }

}
