<?php

namespace atsilex\module\system\tests;

class ProjectStarterTest extends BaseTestCase
{

    private $root = '/tmp/my-project';

    public function testProjectStructure()
    {
        $this->assertFileExists($this->root . '/composer.json');
        $this->assertFileExists($this->root . '/composer.lock');
        $this->assertFileExists($this->root . '/config.default.php');
        $this->assertFileExists($this->root . '/config.php');
        $this->assertFileExists($this->root . '/public/index.php');
        $this->assertFileExists($this->root . '/public/assets/modules/system/css/app.css');
        $this->assertFileExists($this->root . '/files/cache/profiler/.gitignore');
        $this->assertFileExists($this->root . '/files/session/.gitignore');

        $atExtra = json_decode(file_get_contents($this->root . '/composer.json'), true)['extra']['atsilex'];
        $configDefault = file_get_contents($this->root . '/config.default.php');
        foreach ($atExtra as $token => $value) {
            $this->assertNotContains($token, $configDefault);
            $this->assertContains($value, $configDefault);
        }
    }

}
