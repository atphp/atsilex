<?php

namespace atsilex\module\system\tests;

use atsilex\module\system\ModularApp;
use atsilex\module\system\providers\container\AppAwareContainer;

class ContainerTest extends BaseTestCase
{
    public function testContainer()
    {
        $app = $this->getApplication();

        $app->deleteCachedContainerFile();
        $c = $app->getContainer($rebuild = true);

        $this->assertTrue($c instanceof AppAwareContainer);
        $this->assertTrue($c->get('app') instanceof ModularApp);
        $this->assertContains('controllers', $c->getServiceIds());
    }
}
