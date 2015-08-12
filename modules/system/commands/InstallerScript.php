<?php

namespace atsilex\module\system\commands;

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

        $me = new static($root);
        return $me->install();
    }

    public function install()
    {
        // `files` directory
        $this->run('rsync -a %default/files/ %root/files/');
        $this->run('chmod 777 -Rf %root/files');

        // `public` directory
        $this->run('rsync -a %default/public/ %root/public/');

        // Config file
        $this->run('rsync %default/config.default.php %root/config.default.php');
        $this->run('php %root/index.php v3k:generate-config-file');

        // Build assets
        $this->run('php %root/index.php v3k:build-assets');
    }

    private function run($cmd)
    {
        passthru(strtr($cmd, [
            '%default' => dirname(__DIR__) . '/resources/default-app',
            '%root'    => $this->root
        ]));
    }

}
