<?php

namespace atsilex\module\system\providers\container\pass;

use atsilex\module\system\ModularApp;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class AppPass implements CompilerPassInterface
{

    /** @var ModularApp */
    private $app;

    public function __construct(ModularApp $app)
    {
        $this->app = $app;
    }

    public function process(ContainerBuilder $container)
    {
        // Register 'app' service
        $container->setDefinition(
            'app',
            (new Definition())
                ->setClass(ModularApp::class)
                ->setSynthetic(true)
        );

        // Register Silex services
        foreach ($this->app->keys() as $id) {
            $v = $this->app->raw($id);

            if (!is_scalar($v) && !is_array($v)) {
                $container->setDefinition(
                    $id,
                    (new Definition())->setSynthetic(true)
                );
            }
        }
    }

}
