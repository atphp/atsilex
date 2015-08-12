<?php

namespace vendor_name\project_name\test_cases;

use v3knet\module\system\ModularApp;
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

        $this->assertTrue($app instanceof ModularApp);
        $this->assertEquals(APP_ROOT, $app->getAppRoot());
    }

    public function testBuildAssetsCommand()
    {

    }

    public function testComposerBuildRootCommand()
    {
    }

    public function testComposerRebuildCommand()
    {

    }

    public function testConfigFileGeneratatorCommand()
    {

    }

}
