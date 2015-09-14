<?php

namespace atsilex\module\queue\commands;

use atsilex\module\App;
use atsilex\module\system\ModularApp;
use Bernard\QueueFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends Command
{
    /** @var ModularApp */
    protected $app;

    public function __construct(ModularApp $app)
    {
        $this->app = $app;

        $vendor = isset($app['vendor_machine_name']) ? $app['vendor_machine_name'] : 'v3k';
        parent::__construct($vendor . ':queue:list');
    }

    protected function configure()
    {
        $this->setDescription('Message queue consumer');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $queues = $this->app['bernard.queues'];
        foreach ($queues->keys() as $k) {
            $table->addRow([$k, $queues[$k]]);
        }

        $table
            ->setHeaders(['Queue', 'Message class'])
            ->render();
    }
}
