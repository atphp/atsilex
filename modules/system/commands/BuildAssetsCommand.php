<?php

namespace atsilex\module\system\commands;

use atsilex\module\system\ModularApp;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildAssetsCommand extends Command
{

    /** @var  ModularApp */
    protected $app;

    public function __construct(ModularApp $app)
    {
        $this->app = $app;

        parent::__construct('v3k:build-assets');
    }

    protected function configure()
    {
        $this->setDescription('Build module assets');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->app->getModules() as $module) {
            $output->writeln("Building assets for $module module.");
            $this->app->getModule($module)->buildAssets($this->app->getAppRoot() . '/public');
        }
    }

}
