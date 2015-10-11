<?php

namespace atsilex\module\system\commands;

use atsilex\module\system\ModularApp;
use Doctrine\Common\Cache\CacheProvider;
use Silex\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CacheFlushingCommand extends BaseCmd
{
    const NAME        = 'at:cache:flush';
    const DESCRIPTION = 'Flush all caches.';

    /** @var CacheProvider */
    protected $cache;

    public function __construct(ModularApp $app)
    {
        $this->cache = $app->getCache();

        return parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->cache->flushAll()) {
            throw new \RuntimeException('Can not flush cache.');
        }
    }
}
