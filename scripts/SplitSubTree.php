<?php

/**
 * This script is executed by Travis-CI to push changes to sub-trees.
 * -------
 */

if (false !== strpos(phpversion(), '5.6.')) {
    return call_user_func(function () {
        $origin = 'git@github.com:v3knet/v3k.git'; # The main repository
        $branches = ['0.1'];                       # Only push main branches
        $tmp = '/tmp';                             # Temp dir
        $trees = [
            'modules/module'  => 'git@github.com:v3knet/module.git',
            'modules/queue'   => 'git@github.com:v3knet/queue-module.git',
            'modules/swagger' => 'git@github.com:v3knet/swagger-module.git',
            'modules/system'  => 'git@github.com:v3knet/system-module.git',
        ];

        if ($privateKeyUrl = getenv('PRIVATE_KEY_URL')) {
            passthru("touch ~/.ssh/id_rsa");
            $data = file_get_contents($privateKeyUrl);
            file_put_contents('/home/travis/.ssh/id_rsa', $data);
            passthru("chmod 600 ~/.ssh/id_rsa");
        }

        foreach ($trees as $local => $url) {
            foreach ($branches as $branch) {
                passthru(implode('; ', [
                    "rm -rf $tmp/v3k",
                    "git clone -q $origin $tmp/v3k",
                    "cd $tmp/v3k",
                    "git filter-branch -f --prune-empty --subdirectory-filter $local $branch",
                    "git remote add read-only $url",
                    "git push -q -f read-only $branch",
                    "cd -",
                    "rm -rf $tmp/v3k",
                ]));
            }
        }
    });
}
