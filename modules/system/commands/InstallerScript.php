<?php

namespace v3knet\module\system\commands;

use Composer\Script\Event;

/**
 * This is not console command, not suffix with 'Command'.
 *
 * In your composer.json add this command to your scripts.post-install-cmd and scripts.post-update-cmd:
 *
 *  v3knet\module\system\commands\InstallerScript::execute
 */
class InstallerScript
{

    private $root;

    public function __construct($root)
    {
        $this->root = $root;
    }

    public static function execute(Event $event)
    {
        $root = dirname($event->getComposer()->getConfig()->get('vendor-dir'));

        $me = new ComposerInstallScript($root);
        return $me->install();
    }

    public function install()
    {
        $this->createFilesDirectory();
        $this->createPublicDirectory();
        $this->generateConfigFile();
        $this->buildAssets();
    }

    private function createFilesDirectory()
    {
        // TBD
    }

    private function createPublicDirectory()
    {
        // TBD
    }

    public function generateConfigFile()
    {
        // TBD
    }

    private function buildAssets()
    {
        // TBD
    }

}
