<?php

namespace atsilex\module\dev\commands;

use atsilex\module\orm\OrmModule;
use atsilex\module\queue\QueueModule;
use atsilex\module\system\commands\BaseCmd;
use atsilex\module\system\ModularApp;
use atsilex\module\system\SystemModule;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateConfigFileCommand extends BaseCmd
{
    const NAME        = 'at:generate-config-file';
    const DESCRIPTION = 'Generate default config file for your application.';

    /** @var string */
    private $configPath;

    public function __construct(ModularApp $app)
    {
        $this->configPath = $app->getAppRoot() . '/config.php';

        parent::__construct();
    }

    /**
     * @TODO: Provide an option to override current config file.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!is_file($this->configPath)) {
            file_put_contents($this->configPath, $this->generateFileContent());
        }
    }

    private function generateFileContent()
    {
        return sprintf(
            implode("\n", [
                "<?php",
                "",
                "return [",
                "    'debug'   => true,",
                "    'modules' => [",
                "        'orm'    => '%s',",
                "        'queue'  => '%s',",
                "    ],",
                "    # Performance — should disable on dev and enable on production",
                "    # ---------------------",
                "    'cache.magic_services' => false,",
                "] + %s;\n",
            ]),
            OrmModule::class,
            QueueModule::class,
            "require __DIR__ . '/config.default.php'"
        );
    }
}
