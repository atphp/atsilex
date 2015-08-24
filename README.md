Modular Silex [![Build Status](https://travis-ci.org/atphp/atsilex.svg)](https://travis-ci.org/atphp/atsilex)
====

## Built-ins features

1. Twig/Bootstrap/Google analytics/…
- Doctrine Cache, DBAL, ORM
- BernardPHP message queue
- SF2 Console (make your command as service, name it as `anything.command.the_name`, then run `php cli.php`, you see your command is auto registered)
- Module system, check `./modules/system` as example.
- Swagger UI

## Usage

Require `atphp/atsilex` in your project's `composer.json` file:

```json
{
  "name": "v3knet/website",
  "require": {
    "atphp/atsilex": "^0.1.0"
  },
  "scripts": {
    "post-install-cmd": [
      "atsilex\\module\\system\\commands\\InstallerScript::execute"
    ]
  },
  "extra": {
    "atsilex": {
      "%site_name%": "My Project",
      "%site_version%": "1.0-dev",
      "%site_url%": "http://www.vendor-name.com/",
      "%site_frontpage%": "hello",
      "%site_ga_code%": "UA-1234567-890",
      "%vendor_name%": "Vendor Name"
    }
  }
}
```

On composer install `atsilex` will setup default structure for for your application:

```
files/                   # Directory to store temporary files (cache, compiled templates, …)
config.default.php       # (*) Default 
config.php               # The file that return configuration for application.
public/                  # Document root
      /index.php         # (*) Front controller
      /assets/modules/*  # Symlinks for modules's assets
                         # Don't edit (*), they will be overwritten in next composer install.
```

## Write custom module

A module is basically a class which extends `atsilex\module\Module`. Each module can:

1. [Define custom services](https://github.com/atphp/atsilex/blob/0.1/modules/system/resources/docs/DI.md)
- [Define new routes](http://j.mp/1U9Xpwx)
- [Define new commands](http://j.mp/1WOXsSL)
- [Listen to system events](http://j.mp/1WOXutP)

Example:

```php
use atsilex\module\Module;
use Pimple\Container;
use Silex\Application;

class MyModule extends Module {
    protected $machineName = 'my_module';
    protected $name        = 'My Module';
    protected $description = 'Study the how to write module.';

    /**
     * Register my services to DI container.
     */
    public function register(Container $c) {
        $c['my_service'] = function(Container $c) {
            return new MyService($c['my_dependency']);
        };
    }

}
```

Define a module is simple, you also need tell the application about your module — 
edit `config.php`, include your modules there:
 
```php
return [
    // …
    'modules' => [
        'my_module' => 'MyModule',
        'system'    => 'atsilex\module\system\SystemModule', # Can't disable
    ],
    // …
];
```

## Configure database connection

Default database for application is a SQLite file, it's auto created in `files/app.db`
when we run `php public/index.php orm:schema-tool:create` command.

To change default config for database connection, in `config.php`, add code similar to this:

```php
# SQlite
# $db_options = ['driver' => 'pdo_sqlite',  'path' => '/alternative/path/to/app.db'];

# MySQL
$db_options = [
    'driver'    => 'pdo_mysql',
    'host'      => 'mysql_write.someplace.tld',
    'dbname'    => 'my_database',
    'user'      => 'my_username',
    'password'  => 'my_password',
    'charset'   => 'utf8mb4',
];

return [
 // …
 'db.options' => $db_options,
 // …
];
```
