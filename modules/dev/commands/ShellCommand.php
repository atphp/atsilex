<?php

namespace atsilex\module\dev\commands;

use atsilex\module\exceptions\MissingResourceException;
use atsilex\module\system\commands\BaseCmd;
use atsilex\module\system\ModularApp;
use Boris\Boris;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShellCommand extends BaseCmd
{
    const NAME        = 'at:shell';
    const DESCRIPTION = 'Start interacting shell.';

    /** @var  Boris */
    protected $shell;

    public function __construct(ModularApp $app)
    {
        if (!class_exists(Boris::class)) {
            throw new MissingResourceException('Shell command needs d11wtq/boris:^1.0 to run.');
        }

        $this->shell = $app['shell'];

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->shell->start();
    }
}
