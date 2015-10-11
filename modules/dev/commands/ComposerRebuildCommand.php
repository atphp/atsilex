<?php

namespace atsilex\module\dev\commands;

use atsilex\module\system\commands\AppAwareCmd;
use atsilex\module\system\ModularApp;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ComposerRebuildCommand extends AppAwareCmd
{
    const NAME        = 'at:composer-rebuild';
    const DESCRIPTION = 'Rebuild master composer file for all non-core modules.';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $root = $this->app->getAppRoot();
        $json = [];
        foreach ($this->app->getModules() as $name) {
            if (!$this->isCoreModule($name)) {
                $this->mergeModuleComposer($json, $name);
            }
        }

        file_put_contents($root . '/files/composer.json', json_encode((object) $json));
        passthru("composer --working-dir=$root/files update");
    }

    private function isCoreModule($name)
    {
        $ns = $this->app->getModule($name)->getNamespace();

        return !strpos($ns, 'v3knet\\module\\');
    }

    private function mergeModuleComposer(&$json, $name)
    {
        $module = $this->app->getModule($name);
        $moduleDir = $module->getPath();
        $moduleJson = "$moduleDir/composer.json";
        if (file_exists($moduleJson)) {
            $moduleJson = file_get_contents($moduleJson);
            $json = array_merge_recursive($json, json_decode($moduleJson, true));
        }
    }
}
