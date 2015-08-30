<?php

namespace atsilex\module\system\providers\container;

use atsilex\module\system\ModularApp;
use atsilex\module\system\providers\container\pass\AppPass;
use Symfony\Component\DependencyInjection\ContainerBuilder as Builder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

class ContainerBuilder
{

    /** @var  ModularApp */
    private $app;

    /**
     * Builder constructor.
     *
     * @param ModularApp $app
     */
    public function __construct(ModularApp $app)
    {
        $this->app = $app;
    }

    public function build($file, $namespace, $class)
    {
        $container = new Builder();
        $container->addCompilerPass(new AppPass($this->app));
        $container->compile();

        $code = (new PhpDumper($container))->dump([
            'namespace'  => $namespace,
            'class'      => $class,
            'base_class' => '\\' . AppAwareContainer::class,
        ]);

        file_put_contents($file, $code);
    }

}
