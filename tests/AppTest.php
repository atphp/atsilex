<?php

namespace vendor_name\project_name\test_cases;

use Doctrine\Common\Cache\Cache;
use Psr\Log\LoggerInterface;
use v3knet\module\system\tests\BaseTestCase;

class AppTest extends BaseTestCase
{

    public function getApplicationConfiguration()
    {
        $config = ['app.root' => APP_ROOT] + parent::getApplicationConfiguration();

        return $config + require APP_ROOT . '/config.default.php';
    }

    public function testServices()
    {
        $app = $this->getApplication();

        $this->assertEquals(APP_ROOT, $app->getAppRoot());
        $this->assertTrue($app->getCache() instanceof Cache);
        $this->assertTrue($app->getLogger() instanceof LoggerInterface);
    }

}
