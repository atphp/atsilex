Dependency Injection
====

[TBD](https://github.com/silexphp/Pimple).

```php
namespace my_company;

use atsilex\module\Module;
use Pimple\Container;

class MyModule extends Module {
    protected $machineName = 'my_module';
    protected $name        = 'My Module';
    protected $description = 'Study the how to write module.';
    
    public function register(Container $c) {
        $c['my_service'] = function(Container $c) {
            return new MyService($c['my_dependency']);
        };
    }

}
```
