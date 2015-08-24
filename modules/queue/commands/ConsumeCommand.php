<?php

namespace atsilex\module\queue\commands;

use atsilex\module\App;
use atsilex\module\queue\services\Consumer;
use atsilex\module\system\ModularApp;
use Bernard\QueueFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConsumeCommand extends Command
{

    /** @var Consumer */
    protected $consumer;

    /** @var QueueFactory */
    protected $factory;

    /** @var string */
    protected $defaultQueue = null;

    /** @var int */
    protected $defaultLimit = 100;

    public function __construct(ModularApp $app, $name = '%vendor:queue:process')
    {
        $this->consumer = $app['bernard.consumer'];
        $this->factory = $app['bernard.factory'];

        $vendor = isset($app['vendor_machine_name']) ? $app['vendor_machine_name'] : 'v3k';
        parent::__construct(str_replace('%vendor', $vendor, $name));
    }

    protected function configure()
    {
        $this
            ->setDescription('Message queue consumer')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Number of message to be processed.', $this->defaultLimit);

        if (null === $this->defaultQueue) {
            $this->addArgument('queue', InputArgument::REQUIRED, 'Name of message queue.');
        }
        else {
            $this->addArgument('queue', InputArgument::OPTIONAL, 'Name of message queue.', $this->defaultQueue);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->consumer->consume(
            $this->factory->create($queueName = $input->getArgument('queue')),
            [
                'max-runtime'  => PHP_INT_MAX,
                'max-messages' => (int) $input->getOption('limit'),
            ]
        );
    }

}
