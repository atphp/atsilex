<?php

namespace vendor_name\project_name\scripts;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildAssets extends BaseScript
{

    public function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->app->getModules() as $module) {
            $output->writeln("Building assets for $module module.");
            $this->app->getModule($module)->buildAssets($this->app->getAppRoot() . '/public');
        }
    }

}
