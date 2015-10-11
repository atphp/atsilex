<?php

namespace atsilex\module\dev\commands;

use atsilex\module\system\commands\BaseCmd;
use atsilex\module\system\ModularApp;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class PasswordEncodeCommand extends BaseCmd
{
    const NAME        = 'at:password-encode';
    const DESCRIPTION = 'Encode a password.';

    protected function configure()
    {
        $this
            ->addArgument('password', InputArgument::REQUIRED)
            ->addArgument('salt', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $encoder = new MessageDigestPasswordEncoder();
        $password = $input->getArgument('password');
        $salt = $input->getArgument('salt') ?: '';

        $output->writeln('Password: ' . $encoder->encodePassword($password, $salt));
    }
}
