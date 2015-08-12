<?php

namespace v3knet\module\system\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use v3knet\module\queue\QueueModule;
use v3knet\module\system\ModularApp;
use v3knet\module\system\SystemModule;

/**
 * @TODO: Use a better method
 *      — https://github.com/Incenteev/ParameterHandler
 *      - https://getcomposer.org/doc/articles/scripts.md#command-events
 */
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
        file_put_contents(
            $file,
            sprintf(
                "<?php \n\nreturn [\n"
                . "    'debug'   => true,\n"
                . "    'modules' => [\n"
                . "        'queue'  => '%s',\n"
                . "        'system' => '%s',\n"
                . "    ]\n"
                . "] + %s;\n\n",
                QueueModule::class,
                SystemModule::class,
                "require __DIR__ . '/config.default.php'"
            )
        );
    }

}
