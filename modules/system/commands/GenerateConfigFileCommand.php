<?php

namespace atsilex\module\system\commands;

use atsilex\module\queue\QueueModule;
use atsilex\module\system\ModularApp;
use atsilex\module\system\SystemModule;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateConfigFileCommand extends Command
{

    /** @var  ModularApp */
    protected $app;

    public function __construct(ModularApp $app)
    {
        $this->app = $app;

        parent::__construct('v3k:generate-config-file');
    }

    protected function configure()
    {
        $this->setDescription('Generate default config file for your application.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $this->app->getAppRoot() . '/config.php';

        if (!is_file($path)) {
            $this->generate($path);
        }
    }

    private function generate($path)
    {
        file_put_contents($path, sprintf(
            implode("\n", [
                "<?php",
                "// Avoid error when use date() functions.",
                "date_default_timezone_set('UTC');",
                "",
                "return [",
                "    'debug'   => true,",
                "    'modules' => [",
                "        'queue'  => '%s', # Can disable",
                "        'system' => '%s', # Can't disable",
                "    ],",
                "    # Performance — should disable on dev and enable on production",
                "    # ---------------------",
                "    'cache.magic_services' => false,",
                "] + %s;\n",
            ]),
            QueueModule::class,
            SystemModule::class,
            "require __DIR__ . '/config.default.php'"
        ));
    }

}
