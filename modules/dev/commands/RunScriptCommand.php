<?php

namespace atsilex\module\dev\commands;

use atsilex\module\App;
use atsilex\module\system\commands\AppAwareCmd;
use Silex\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunScriptCommand extends AppAwareCmd
{
    const NAME        = 'at:run-script';
    const DESCRIPTION = 'Run a script.';

    protected function configure()
    {
        $this->addArgument('script', null, InputArgument::REQUIRED, 'Script class');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $class = $input->getArgument('script');
        if (!class_exists($class)) {
            throw new \RuntimeException('Can not load class: ' . $class);
        }

        $script = new $class($this->getApp());
        $script->execute($input, $output);
    }
}
