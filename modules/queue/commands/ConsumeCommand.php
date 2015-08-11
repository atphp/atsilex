<?php

namespace v3knet\module\queue\commands;

use Bernard\Consumer;
use Bernard\Event\RejectEnvelopeEvent;
use Bernard\Message\DefaultMessage;
use Bernard\QueueFactory;
use Pimple\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use v3knet\module\App;

class ConsumeCommand extends Command
{

    /** @var  Container */
    protected $c;

    /** @var  Consumer */
    protected $consumer;

    /** @var  QueueFactory */
    protected $queueFactory;

    public function __construct(Container $c)
    {
        $this->c = $c;
        $this->consumer = $c['bernard.consumer'];
        $this->queueFactory = $c['bernard.factory'];

        parent::__construct('project-name:consume');
    }

    protected function configure()
    {
        $this
            ->setDescription('Message queue consumer')
            ->addOption('queue', null, InputOption::VALUE_REQUIRED, 'Name of message queue.')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Number of message to be processed.', 100);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var SimpleRouter $router */
        $router = $this->consumer->getRouter();
        $queueName = $input->getOption('queue');
        $queue = $this->queueFactory->create($queueName);

        // Raise error instead of silently ignore it
        $this->consumer->getDispatcher()->addListener('bernard.reject', function (RejectEnvelopeEvent $event) {
            throw $event->getException();
        });

        // Route to our own processMessage method
        $router->add($queueName, function (DefaultMessage $message) use ($output) {
            $this->processMessage($output, $message);
        });

        $this->consumer->consume($queue, [
            'max-runtime'  => PHP_INT_MAX,
            'max-messages' => (int) $input->getOption('limit'),
        ]);
    }

    public function processMessage(OutputInterface $output, DefaultMessage $message)
    {
        throw new \RuntimeException('Implement logic here.');
    }

}
