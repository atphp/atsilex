<?php

namespace atsilex\module\system\commands;

use Composer\Script\Event;

/**
 * This is not console command, not suffix with 'Command'.
 *
 * In your composer.json add this command to your scripts.post-install-cmd and scripts.post-update-cmd:
 *
 *  atsilex\module\system\commands\InstallerScript::execute
 *
 */
class InstallerScript
{

    private $root;
    private $systemModuleDir;

    public function __construct($root)
    {
        $this->root = $root;
        $this->systemModuleDir = dirname(__DIR__);
    }

    public static function execute(Event $event)
    {
        $root = dirname($event->getComposer()->getConfig()->get('vendor-dir'));

        return (new static($root))->install($event);
    }

    /**
     * @TODO Use PHP to create files instead of bash commands.
     * @param Event $event
     */
    public function install(Event $event)
    {
        $cli = 'php %root/public/index.php';

        $this->setupFileStructure($cli);

        // Find and replace tokens defined in composer's extra
        $extras = $event->getComposer()->getPackage()->getExtra();
        if (isset($extras) && !empty($extras['atsilex'])) {
            $this->buildDefaultConfig($extras);
        }

        $this->run("$cli v3k:install");
    }

    private function setupFileStructure($cli)
    {
        $this
            ->run('rsync -a %default/files/ %root/files/')
            ->run('chmod -Rf 777 %root/files')
            ->run('mkdir -p %root/public/assets/modules/')
            ->run('rsync %default/public/index.php %root/public/index.php')
            ->run('rsync %default/config.default.php %root/config.default.php')
            ->run("$cli v3k:generate-config-file")
            ->run("$cli orm:schema-tool:update --force")
            ->run("$cli v3k:build-assets")
            ->run("$cli v3k:cache:flush");
    }

    private function buildDefaultConfig(array $extras)
    {
        file_put_contents(
            "$this->root/config.default.php",
            strtr(
                file_get_contents("$this->root/config.default.php"),
                $extras['atsilex']
            )
        );
    }

    private function run($cmd)
    {
        passthru(strtr($cmd, [
            '%default' => $this->systemModuleDir . '/resources/default-app',
            '%root'    => $this->root
        ]));

        return $this;
    }

}
