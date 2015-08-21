Queue module [![Build Status](https://travis-ci.org/v3knet/queue-module.svg)](https://travis-ci.org/v3knet/queue-module)
====

With this module, we can put the message to queue, process the message using consumer.

## 1. Let the module know your queue

```php
use atsilex\module\Module;
use Pimple\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Bernard\Message\DefaultMessage;

class MyModule extends Module {
    public function subscribe(Container $container, EventDispatcherInterface $dispatcher) {
        $dispatcher->addListener('@queue.queues.get', function (GenericEvent $event) {
            $queues = $event->getArgument('queues');
            $queues['my_module.demo_queue'] = DefaultMessage::class;
        });
    }
}
```

Then your queue is listed on:

```
php public/index.php v3k:queue:list
```

## 2. Produce message

```php
use atsilex\module\system\ModularAp;
use Bernard\Message\DefaultMessage;

$msg = new DefaultMessage('my_module.demo_queue, ['foo' => 'bar']);
$app['bernard.producer']->produce($msg);
```

## 3. Process the message 

We need to teach consumer how to route our message:
 
```php
use atsilex\module\Module;
use Bernard\Router\SimpleRouter;
use Bernard\Message\DefaultMessage;
use Pimple\Container;

class MyModule extends Module {
    public function register(Container $c)
    {
        $c->extend('bernard.router', function (SimpleRouter $router, Container $c) {
            $router->add('my_module.demo_queue', function (DefaultMessage $m) use ($c) {
                // Logic to process the messasge
            });
            
            return $router;
        });
    }
}
```

The message can now be routed correctly, now to process the message, just call the consume command:
 
```
php public/index.php v3k:queue:process my_module.demo_queue
```
