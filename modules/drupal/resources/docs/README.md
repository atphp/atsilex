TBD
====

## Controller

Check `atphp\module\drupal\DrupalController` as an example controller.

## Config

```php
# @file config.php

return call_user_func(function(){
  $config = require __DIR__ . '/config.default.php';
  
  return [
    'drupal.options' => [
        'root'     => '/data/disk/at/static/alarm',
        'site_dir' => '/data/disk/at/static/alarm/sites/alarm-drupal.drupal.work',
        'base_url' => 'http://alarm-drupal.drupal.work',
        'global'   => [
            'databases' => [
                'default' => [
                    'default' => [
                        'driver'   => 'mysql',
                        'database' => 'somedb',
                        'username' => 'someuser',
                        'password' => '***',
                        'host'     => 'localhost',
                    ]
                ]
            ]
        ],
        'conf'     => [],
    ]
  ] + $config;
});
```
