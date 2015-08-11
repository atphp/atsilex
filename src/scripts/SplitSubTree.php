<?php

if (false !== strpos(phpversion(), '5.6.')) {
  return call_user_func(function () {
      $origin = 'git@github.com:v3knet/v3k.git';
      $branches = ['0.1'];
      $tmp = '/tmp';
      $trees = [
          'modules/swagger' => 'git@github.com:v3knet/swagger-module.git',
          'modules/system'  => 'git@github.com:v3knet/system-module.git',
          'modules/queue'   => 'git@github.com:v3knet/queue-module.git',
      ];

      if ($privateKeyUrl = getenv('PRIVATE_KEY_URL')) {
        passthru("touch ~/.ssh/id_rsa");
        passthru("wget $privateKeyUrl -O ~/.ssh/id_rsa");
      }

      foreach ($trees as $local => $url) {
        foreach ($branches as $branch) {
          passthru(implode('; ', [
            "rm -rf $tmp/v3k",
            "git clone $origin $tmp/v3k",
            "cd $tmp/v3k",
            "git filter-branch -f --prune-empty --subdirectory-filter $local $branch",
            "git remote add read-only $url",
            "git push -f read-only $branch",
            "cd -",
            "rm -rf $tmp/v3k",
          ]));
        }
      }

      require_once __DIR__ . '/ComposerBuildRoot.php';

      passthru('composer update -vv');
  });
}
