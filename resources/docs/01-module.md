Module
====

A module can provide services, commands and listen to system events.

To create a module, extends the base module class:

```php
<?php

namespace my_company\my_module;

use atsilex\module\Module;
use Pimple\Container;

class MyModule extends Module {
    protected $machineName = 'my_module';
    protected $name        = 'My Module';

    public function register(Container $c) {
        $c['my_service'] = function(Container $c) {
            return new MyService($c['foo']);
        };
    }
}
```
