<?php

namespace atsilex\module\system\commands;

use atsilex\module\exceptions\MissingResourceException;
use atsilex\module\system\ModularApp;
use Boris\Boris;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShellCommand extends Command
{
    /** @var  ModularApp */
    protected $app;

    /** @var  Boris */
    protected $shell;

    public function __construct(ModularApp $app)
    {
        if (!class_exists(Boris::class)) {
            throw new MissingResourceException('Shell command needs d11wtq/boris:^1.0 to run.');
        }

        $this->shell = $app['shell'];

        $vendor = isset($app['vendor_machine_name']) ? $app['vendor_machine_name'] : 'v3k';
        parent::__construct($vendor . ':shell');

        $this->setDescription('Start interacting shell.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->shell->start();
    }
}
