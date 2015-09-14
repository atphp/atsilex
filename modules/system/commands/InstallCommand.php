<?php

namespace atsilex\module\system\commands;

use atsilex\module\system\events\AppEvent;
use atsilex\module\system\ModularApp;
use atsilex\module\system\SystemModule;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @TODO: How to hide this command to end user?
 */
class InstallCommand extends Command
{
    /** @var  ModularApp */
    protected $app;

    public function __construct(ModularApp $app)
    {
        $this->app = $app;

        $vendor = isset($app['vendor_machine_name']) ? $app['vendor_machine_name'] : 'v3k';
        parent::__construct($vendor . ':install');
    }

    protected function configure()
    {
        $this->setDescription('Install the system');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this
            ->app
            ->getDispatcher()
            ->dispatch(SystemModule::EVENT_APP_INSTALL, new AppEvent($this->app));
    }
}
