<?php

namespace atsilex\module\system\tests\commands;

use atsilex\module\dev\commands\RunScriptCommand;
use atsilex\module\system\tests\BaseTestCase;
use Symfony\Component\Console\Application as Console;

class ConsoleTest extends BaseTestCase
{
    /**
     * Make sure the magic commands are auto added to console.
     */
    public function testConsole()
    {
        $console = $this->getApplication()->getConsole();

        $this->assertTrue($console instanceof Console);
        $this->assertTrue($console->find('at:run-script') instanceof RunScriptCommand);
    }
}
