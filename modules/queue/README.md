Queue module [![Build Status](https://travis-ci.org/v3knet/queue-module.svg)](https://travis-ci.org/v3knet/queue-module)
====

With this module, we can put the message to queue, process the message using consumer.

## 1. Let the module know your queue

```php
use atsilex\module\Module;
use atsilex\module\system\events\AppEvent;
use Pimple\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Bernard\Message\DefaultMessage;

class MyModule extends Module {
    public function subscribe(Container $container, EventDispatcherInterface $dispatcher) {
        $dispatcher->addListener('queue.queues.get', function (AppEvent $event) {
            $queues = $event->getSubject();
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
use atsilex\module\system\events\AppEvent;
use Bernard\Router\SimpleRouter;
use Bernard\Message\DefaultMessage;
use Pimple\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MyModule extends Module {
    public function subscribe(Container $container, EventDispatcherInterface $dispatcher) {
        // …
        $dispatcher->addListener('queue.router.create', function (AppEvent $event) {
            $router = $event->getSubject();
            $router->add('my_module.demo_queue', function (ImportMessage $m) use ($c) {
                // Logic to process the message
            });
        });
    }
}
```

The message can now be routed correctly, now to process the message, just call the consume command:
 
```
php public/index.php v3k:queue:process my_module.demo_queue
```
