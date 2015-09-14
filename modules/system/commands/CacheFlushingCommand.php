<?php

namespace atsilex\module\system\commands;

use atsilex\module\system\ModularApp;
use Doctrine\Common\Cache\CacheProvider;
use Silex\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CacheFlushingCommand extends Command
{
    /** @var  ModularApp */
    protected $app;

    /** @var CacheProvider */
    protected $cache;

    public function __construct(ModularApp $app)
    {
        $this->cache = $app->getCache();

        $vendor = isset($app['vendor_machine_name']) ? $app['vendor_machine_name'] : 'v3k';
        parent::__construct($vendor . ':cache:flush');
    }

    protected function configure()
    {
        $this->setDescription('Flush all caches');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->cache->flushAll()) {
            throw new \RuntimeException('Can not flush cache.');
        }
    }
}
