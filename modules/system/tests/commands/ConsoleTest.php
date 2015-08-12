<?php

namespace atsilex\module\system\tests\commands;

use atsilex\module\system\commands\RunScriptCommand;
use atsilex\module\system\tests\BaseTestCase;
use atsilex\module\system\tests\fixtures\modules\foo\commands\FooCommand;
use Symfony\Component\Console\Application as Console;
use Symfony\Component\Console\Tester\CommandTester;

class ConsoleTest extends BaseTestCase
{

    /**
     * Make sure the magic commands are auto added to console.
     */
    public function testConsole()
    {
        $console = $this->getApplication()->getConsole();

        $this->assertTrue($console instanceof Console);
        $this->assertTrue($console->find('v3k:run-script') instanceof RunScriptCommand);
    }

    public function testRunScriptCommand()
    {
        $cmd = $this
            ->getApplication()
            ->getConsole()
            ->find('v3k:run-script');

        $tester = new CommandTester($cmd);
        $tester->execute([
            'command' => 'v3k:run-script',
            'script'  => FooCommand::class,
        ]);

        $this->assertTrue(FooCommand::$executed);
    }

}
