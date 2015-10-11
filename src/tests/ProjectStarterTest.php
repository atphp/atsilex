<?php

namespace atsilex\tests;

class ProjectStarterTest extends BaseTestCase
{
    private $root = '/tmp/my-project';

    public function testProjectStarter()
    {
        if (!is_file($this->root . '/composer.json')) {
            return $this->markTestSkipped('This test is slow, only on Travis, check .travis.yml');
        }

        $this->doTestProjectStarter($this->root);
    }

    private function doTestProjectStarter($root)
    {
        $this->assertFileExists($root . '/composer.json');
        $this->assertFileExists($root . '/composer.lock');
        $this->assertFileExists($root . '/config.default.php');
        $this->assertFileExists($root . '/config.php');
        $this->assertFileExists($root . '/public/index.php');
        $this->assertFileExists($root . '/public/assets/modules/system/css/app.css');
        $this->assertFileExists($root . '/files/cache/profiler/.gitignore');
        $this->assertFileExists($root . '/files/session/.gitignore');

        $atExtra = json_decode(file_get_contents($root . '/composer.json'), true)['extra']['atsilex'];
        $configDefault = file_get_contents($root . '/config.default.php');
        foreach ($atExtra as $token => $value) {
            $this->assertNotContains($token, $configDefault);
            $this->assertContains($value, $configDefault);
        }
    }
}
