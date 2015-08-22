<?php

namespace atsilex\module\queue\test_cases;

use atsilex\module\system\ModularApp;
use atsilex\module\queue\QueueModule;
use atsilex\module\test_cases\BaseTestCase;
use Bernard\Consumer;
use Bernard\Driver;
use Bernard\Producer;
use Bernard\QueueFactory\PersistentFactory;
use Bernard\Router;
use Bernard\Serializer;
use Silex\Application;

class QueueModuleTest extends \PHPUnit_Framework_TestCase
{

    protected function getContainer()
    {
        $c = new ModularApp(['app.root' => '/tmp']);
        $c->register(new QueueModule());

        return $c;
    }

    public function testServiceDefinitions()
    {
        $c = $this->getContainer();

        $this->assertTrue($c['bernard.driver'] instanceof Driver);
        $this->assertTrue($c['bernard.factory'] instanceof PersistentFactory);
        $this->assertTrue($c['bernard.serializer'] instanceof Serializer);
        $this->assertTrue($c['bernard.consumer'] instanceof Consumer);
        $this->assertTrue($c['bernard.producer'] instanceof Producer);
        $this->assertTrue($c['bernard.router'] instanceof Router);
    }

    /**
     * @TODO: Review @bernard.ComsumerCommand
     */
    public function testConsumeCommand()
    {
        // $this->assertTrue($c['@queue.cmd.consume'] instanceof ConsumeCommand);
        // $console = new Console();
        // $console->add(new new ConsumeCommand());
        // $this->assertTrue(false);
    }

}
