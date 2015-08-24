Routing
====

## PHP Routing

```php
namespace my_company;

use atsilex\module\Module;
use Silex\Application;
use Silex\ControllerCollection;

class MyModule extends Module {
    protected $machineName = 'my_module';
    protected $name        = 'My Module';
    protected $description = 'Study the how to write module.';
    
    public function connect(Application $app) {
        $route = $app['controllers_factory']; /** @var ControllerCollection $route */
        
        // @system.ctrl.home:actionGet-> my_company\controllers\HomeController::actionGet
        $route->get('/hello', '@system.ctrl.home:actionGet')->bind('hello');
        
        $route->get('/hi', function() {
            return 'Hi there!';
        })->bind('hello');
        
        return $route;
    } 
}
```

## Yaml routing

First you needs to tell that you use YAML file to define routes:

```php
namespace my_company;

use atsilex\module\Module;
use Silex\Application;
use Silex\ControllerCollection;

class MyModule extends Module {
    protected $machineName = 'my_module';
    protected $routeFile   = true;
}
```

Then define you routes, default location is your module's `resources/config/routing.yml`:

```yaml
hello-yaml:
  path: /hello-yaml
  methods: [GET]
  defaults: { _controller: @foo.ctrl.hello:actionGet }
```
