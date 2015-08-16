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

    public function __construct(ModularApp $app)
    {
        $this->consumer = $app['bernard.consumer'];
        $this->factory = $app['bernard.factory'];

        parent::__construct('v3k:queue:process');
    }

    protected function configure()
    {
        $this
            ->setDescription('Message queue consumer')
            ->addArgument('queue', InputArgument::REQUIRED, 'Name of message queue.')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Number of message to be processed.', 100);
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
