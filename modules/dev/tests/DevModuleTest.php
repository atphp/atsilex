<?php

namespace atsilex\module\dev\tests;

use atsilex\module\system\tests\BaseTestCase;
use atsilex\module\system\tests\fixtures\modules\foo\commands\FooCommand;
use Symfony\Component\Console\Tester\CommandTester;

class DevModuleTest extends BaseTestCase
{
    public function testModule()
    {
        // …
    }

    public function testRunScriptCommand()
    {
        $cmd = $this->getApplication()->getConsole()->find('at:run-script');

        $tester = new CommandTester($cmd);
        $tester->execute(['command' => 'at:run-script', 'script' => FooCommand::class]);

        $this->assertTrue(FooCommand::$executed);
    }
}
