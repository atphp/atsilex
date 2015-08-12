<?php

namespace v3knet\module\system\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use v3knet\module\system\ModularApp;

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
        $this
            ->setDescription('Build module assets')
            ->addArgument('script', null, InputArgument::REQUIRED, 'Script class');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->app->getModules() as $module) {
            $output->writeln("Building assets for $module module.");
            $this->app->getModule($module)->buildAssets($this->app->getAppRoot() . '/public');
        }
    }

}
