<?php

namespace atsilex\module\dev\commands;

use atsilex\module\system\commands\AppAwareCmd;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScriptCommand extends AppAwareCmd
{
    const NAME        = 'at:scr';
    const DESCRIPTION = 'Execute a PHP file.';

    protected function configure()
    {
        $this->addArgument('file', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($result = require $input->getArgument('file')) {
            if (is_callable($result)) {
                call_user_func($result, $this->getApp());
            }
        }
    }
}
