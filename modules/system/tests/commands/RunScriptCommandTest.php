<?php

namespace v3knet\module\system\tests;

use Symfony\Component\Console\Tester\CommandTester;
use v3knet\module\system\tests\fixtures\modules\foo\commands\FooCommand;

class RunScriptCommandTest extends BaseTestCase
{

    public function testCommand()
    {
        $app = $this->getApplication();
        $console = $app->getConsole();
        $console->add($cmd = $app['@system.cmd.run_script']);

        $tester = new CommandTester($cmd);
        $tester->execute([
            'command' => 'v3k:run-script',
            'script'  => FooCommand::class,
        ]);

        $this->assertTrue(FooCommand::$executed);
    }

}
