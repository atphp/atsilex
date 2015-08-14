<?php

namespace atsilex\module\system\commands;

use Composer\Script\Event;

/**
 * This is not console command, not suffix with 'Command'.
 *
 * In your composer.json add this command to your scripts.post-install-cmd and scripts.post-update-cmd:
 *
 *  v3knet\module\system\commands\InstallerScript::execute
 *
 * @TODO Use PHP to create files instead of bash commands.
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
        return $me->install($event);
    }

    public function install(Event $event)
    {
        // Put default files in place.
        $this
            // `files` directory
            ->run('rsync -a %default/files/ %root/files/')
            ->run('chmod -Rf 777 %root/files')
            // `public` directory
            ->run('mkdir -p %root/public/assets/modules/')
            ->run('rsync %default/public/index.php %root/public/index.php')
            // Config file
            ->run('rsync %default/config.default.php %root/config.default.php')
            ->run('php %root/public/index.php v3k:generate-config-file')
            // Build assets
            ->run('php %root/public/index.php v3k:build-assets');

        // Find and replace tokens defined in composer's extra
        $extras = $event->getComposer()->getPackage()->getExtra();
        if (isset($extras) && !empty($extras['atsilex'])) {
            $code = file_get_contents("$this->root/config.default.php");
            file_put_contents("$this->root/config.default.php", strtr($code, $extras['atsilex']));
        }
    }

    private function run($cmd)
    {
        $cmd = strtr($cmd, [
            '%default' => dirname(__DIR__) . '/resources/default-app',
            '%root'    => $this->root
        ]);

        passthru($cmd);

        return $this;
    }

}
