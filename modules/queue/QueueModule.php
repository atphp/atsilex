<?php

namespace atsilex\module\queue;

use atsilex\module\Module;
use atsilex\module\queue\services\Consumer;
use atsilex\module\system\events\AppEvent;
use atsilex\module\system\ModularApp;
use Bernard\Driver\FlatFileDriver;
use Bernard\Event\RejectEnvelopeEvent;
use Bernard\Middleware\MiddlewareBuilder;
use Bernard\Normalizer\DefaultMessageNormalizer;
use Bernard\Normalizer\EnvelopeNormalizer;
use Bernard\Producer;
use Bernard\QueueFactory\PersistentFactory;
use Bernard\Router\SimpleRouter;
use Bernard\Serializer;
use Normalt\Normalizer\AggregateNormalizer;
use Pimple\Container;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

/**
 * @TODO: Integrate https://github.com/bernardphp/Juno
 * @TODO: Document how to change queue backend.
 */
class QueueModule extends Module
{

    protected $machineName = 'queue';
    protected $name        = 'Queue';

    /**
     * {@inheritdoc}
     */
    public function register(Container $c)
    {
        $c['bernard.driver'] = function (Container $c) {
            return new FlatFileDriver($c['app.root'] . '/files/queue');
        };

        $c['bernard.serializer'] = function (Container $c) {
            return new Serializer(new AggregateNormalizer([
                new DefaultMessageNormalizer(),
                new EnvelopeNormalizer(),
                new GetSetMethodNormalizer()
            ]));
        };

        $c['bernard.factory'] = function (Container $c) {
            $driver = $c['bernard.driver'];
            $serializer = $c['bernard.serializer'];
            return new PersistentFactory($driver, $serializer);
        };

        $c['bernard.producer'] = function (Container $c) {
            $factory = $c['bernard.factory'];
            $dispatcher = $c['dispatcher'];
            return new Producer($factory, $dispatcher);
        };

        $c['bernard.queues'] = function (ModularApp $c) {
            return $this->getQueues($c);
        };

        $c['bernard.router'] = function (ModularApp $c) {
            return $this->getRouter($c);
        };

        $c['bernard.consumer'] = function (Container $c) {
            $router = $c['bernard.router'];
            $dispatcher = $c['dispatcher'];

            // Raise error instead of silently ignore it
            // @TODO: Update docs
            if (isset($c['queue.consumer.throw_error']) && !empty($c['queue.consumer.throw_error'])) {
                $dispatcher->addListener(
                    'bernard.reject',
                    function (RejectEnvelopeEvent $event) {
                        throw $event->getException();
                    }
                );
            }

            return new Consumer($router, $dispatcher);
        };
    }

    private function getRouter(ModularApp $c)
    {
        $router = new SimpleRouter();

        $c
            ->getDispatcher()
            ->dispatch('queue.router.create', new AppEvent($c, $router));

        return $router;
    }

    private function getQueues(ModularApp $c)
    {
        $queueNames = new Container();

        $c
            ->getDispatcher()
            ->dispatch('queue.queues.get', new AppEvent($c, $queueNames));

        return $queueNames;
    }

}
