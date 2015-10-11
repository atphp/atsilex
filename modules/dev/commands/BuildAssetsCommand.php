<?php

namespace atsilex\module\dev\commands;

use atsilex\module\system\commands\AppAwareCmd;
use atsilex\module\system\ModularApp;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildAssetsCommand extends AppAwareCmd
{
    const NAME        = 'at:build-assets';
    const DESCRIPTION = 'Build module assets.';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->app->getModules() as $module) {
            $output->writeln("Building assets for $module module.");
            $this->app->getModule($module)->buildAssets($this->app->getAppRoot() . '/public');
        }
    }
}
