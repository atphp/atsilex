<?php

namespace v3knet\module\system\commands;

use Silex\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use v3knet\module\App;

class RunScriptCommand extends Command
{

    /** @var  Application */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
        parent::__construct('v3k:run-script');
    }

    protected function configure()
    {
        $this
            ->setDescription('Run a script')
            ->addArgument('script', null, InputArgument::REQUIRED, 'Script class');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $class = $input->getArgument('script');
        $script = new $class($this->app);
        $script->execute($input, $output);
    }

}
