<?php

namespace atsilex\module\queue\commands;

use atsilex\module\App;
use atsilex\module\queue\services\Consumer;
use atsilex\module\system\ModularApp;
use Bernard\Event\RejectEnvelopeEvent;
use Bernard\QueueFactory;
use Bernard\Router\SimpleRouter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConsumeCommand extends Command
{

    /** @var ModularApp */
    protected $app;

    /** @var Consumer */
    protected $consumer;

    /** @var QueueFactory */
    protected $queueFactory;

    /** @var SimpleRouter */
    protected $queueRouter;

    public function __construct(ModularApp $app)
    {
        $this->app = $app;
        $this->consumer = $app['bernard.consumer'];
        $this->queueFactory = $app['bernard.factory'];
        $this->queueRouter = $this->consumer->getRouter();

        parent::__construct('v3k:queue');
    }

    protected function configure()
    {
        $this
            ->setDescription('Message queue consumer')
            ->addArgument('action', InputArgument::OPTIONAL, 'Command action', 'help')
            ->addOption('queue', null, InputOption::VALUE_OPTIONAL, 'Name of message queue.')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Number of message to be processed.', 100);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        switch ($input->getArgument('action')) {
            case 'list':
                return $this->listQueues($input, $output);

            case 'process':
                return $this->process($input, $output);

            default:
                return $output->writeln('Available actions: list, process');
        }
    }

    protected function listQueues(InputInterface $input, OutputInterface $output)
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

    protected function process(InputInterface $input, OutputInterface $output)
    {
        $queue = $this->queueFactory->create($queueName = $input->getOption('queue'));

        $this->consumer->consume($queue, [
            'max-runtime'  => PHP_INT_MAX,
            'max-messages' => (int) $input->getOption('limit'),
        ]);
    }

}
