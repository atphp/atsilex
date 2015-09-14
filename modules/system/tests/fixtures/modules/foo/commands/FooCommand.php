<?php

namespace atsilex\module\system\tests\fixtures\modules\foo\commands;

class FooCommand
{
    public static $executed = false;

    public function execute()
    {
        FooCommand::$executed = true;
    }
}
