Event system
====

[TBD](http://symfony.com/doc/current/components/event_dispatcher/index.html)

A module can listen to any system's events:

```php
namespace my_company;

use atsilex\module\Module;
use Pimple\Container;
use Silex\ControllerCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\Event;

class MyModule extends Module {
    protected $machineName = 'my_module';
    protected $routeFile   = true;
    
    public function subscribe(Container $container, EventDispatcherInterface $dispatcher) {
        $dispatcher
            ->addListener(
                'event_x', 
                function (Event $event) {
                    // my logic
                }
            );
    }
}
```

## Dispatch an event

TBD.
