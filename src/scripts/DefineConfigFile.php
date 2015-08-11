<?php

namespace vendor_name\project_name\scripts;

use v3knet\module\queue\QueueModule;
use v3knet\module\system\SystemModule;

/**
 * @TODO: Use a better method
 *      — https://github.com/Incenteev/ParameterHandler
 *      - https://getcomposer.org/doc/articles/scripts.md#command-events
 */
class DefineConfigFile
{

    public static function execute()
    {
        $file = __DIR__ . '/../../config.php';

        if (!is_file($file)) {
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

}

DefineConfigFile::execute();
