<?php

/**
 * This script is executed by Travis-CI to push changes to sub-trees.
 * -------
 */

namespace atsilex\scripts;

class SplitSubTree
{
    private $privateKeyUrl;
    private $origin   = 'git@github.com:atphp/atsilex.git';
    private $branches = ['0.3'];
    private $trees    = [
        'src/module'      => 'git@github.com:v3knet/module.git',
        'modules/dev'     => 'git@github.com:v3knet/dev-module.git',
        'modules/orm'     => 'git@github.com:v3knet/orm-module.git',
        'modules/queue'   => 'git@github.com:v3knet/queue-module.git',
        'modules/swagger' => 'git@github.com:v3knet/swagger-module.git',
        'modules/system'  => 'git@github.com:v3knet/system-module.git',
    ];

    /**
     * SplitSubTree constructor.
     */
    public function __construct()
    {
        $this->privateKeyUrl = getenv('PRIVATE_KEY_URL');
    }

    public function execute()
    {
        $this->configureKeys();

        foreach ($this->trees as $local => $url) {
            foreach ($this->branches as $branch) {
                $this->split($local, $branch, $url);
            }
        }
    }

    private function configureKeys()
    {
        passthru("touch ~/.ssh/id_rsa");
        $data = file_get_contents($this->privateKeyUrl);
        file_put_contents('/home/travis/.ssh/id_rsa', $data);
        passthru("chmod 600 ~/.ssh/id_rsa");
    }

    private function split($local, $branch, $url, $tmp = '/tmp')
    {
        passthru(implode('; ', [
            "rm -rf $tmp/v3k",
            "git clone -q --single-branch --branch=$branch $this->origin $tmp/v3k",
            "cd $tmp/v3k",
            "git filter-branch -f --prune-empty --subdirectory-filter $local $branch",
            "git remote add read-only $url",
            "git push -q -f --tags read-only $branch",
            "cd -",
            "rm -rf $tmp/v3k",
        ]));
    }
}

if (false !== strpos(phpversion(), '5.6.')) {
    return call_user_func(function () {
        return (new SplitSubTree())->execute();
    });
}
