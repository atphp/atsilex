<?php

namespace atsilex\module\system\commands;

use atsilex\module\system\ModularApp;
use Silex\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CacheFlushingCommand extends Command
{

    /** @var  ModularApp */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;

        $vendor = isset($app['vendor_machine_name']) ? $app['vendor_machine_name'] : 'v3k';
        parent::__construct($vendor . ':cache-flush');
    }

    protected function configure()
    {
        $this->setDescription('Flush all caches');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app->getCache()->deleteAll();
    }

}
