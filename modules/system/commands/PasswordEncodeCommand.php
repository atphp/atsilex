<?php

namespace atsilex\module\system\commands;

use atsilex\module\system\ModularApp;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class PasswordEncodeCommand extends Command
{
    /** @var  ModularApp */
    protected $app;

    public function __construct(ModularApp $app)
    {
        $this->app = $app;

        $vendor = isset($app['vendor_machine_name']) ? $app['vendor_machine_name'] : 'v3k';
        parent::__construct($vendor . ':password-encode');
    }

    protected function configure()
    {
        $this
            ->setDescription('Encode a password')
            ->addArgument('password', InputArgument::REQUIRED)
            ->addArgument('salt', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $encoder = new MessageDigestPasswordEncoder();
        $password = $input->getArgument('password');
        $salt = $input->getArgument('salt') ?: '';

        $output->writeln('Passord: ' . $encoder->encodePassword($password, $salt));
    }
}
