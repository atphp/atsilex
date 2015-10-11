<?php

namespace atsilex\module\dev\commands;

use atsilex\module\system\commands\AppAwareCmd;
use atsilex\module\system\events\AppEvent;
use atsilex\module\system\ModularApp;
use atsilex\module\system\SystemModule;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A simple command which dispatch install event, modules can listen to this event to provide the install script.
 */
class InstallCommand extends AppAwareCmd
{
    const NAME        = 'at:install';
    const DESCRIPTION = 'Install the system.';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this
            ->getApp()
            ->getDispatcher()
            ->dispatch(SystemModule::EVENT_APP_INSTALL, new AppEvent($this->getApp()));
    }
}
