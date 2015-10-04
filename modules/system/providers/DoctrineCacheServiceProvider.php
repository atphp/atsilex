<?php

namespace atsilex\module\system\providers;

use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\MemcacheCache;
use Doctrine\Common\Cache\MemcachedCache;
use Doctrine\Common\Cache\CouchbaseCache;
use Doctrine\Common\Cache\XcacheCache;
use Doctrine\Common\Cache\RedisCache;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DoctrineCacheServiceProvider implements ServiceProviderInterface
{
    public function register(Container $c)
    {
        $c['orm.default_cache'] = [
            'driver' => 'array',
        ];

        $c['orm.cache.locator'] = $c->protect(function ($name, $cacheName, $options) use ($c) {
            return $this->cacheLocator($c, $name, $cacheName, $options);
        });

        $this->registerCacheFactory($c);
    }

    private function registerCacheFactory(Container $c)
    {
        $c['orm.cache.factory'] = $c->protect(function ($driver, $options) use ($c) {
            return $this->cacheFactory($c, $driver, $options);
        });

        $c['orm.cache.factory.backing_memcache'] = $c->protect(function () {
            return new \Memcache;
        });

        $c['orm.cache.factory.memcache'] = $c->protect(function ($options) use ($c) {
            return $this->createMemcacheCache($c, $options);
        });

        $c['orm.cache.factory.backing_memcached'] = $c->protect(function () {
            return new \Memcached;
        });

        $c['orm.cache.factory.memcached'] = $c->protect(function ($options) use ($c) {
            return $this->createMemcachedCache($c, $options);
        });

        $c['orm.cache.factory.backing_redis'] = $c->protect(function () {
            return new \Redis;
        });

        $c['orm.cache.factory.redis'] = $c->protect(function ($options) use ($c) {
            return $this->createRedisCache($c, $options);
        });

        $c['orm.cache.factory.array'] = $c->protect(function () {
            return new ArrayCache;
        });

        $c['orm.cache.factory.apc'] = $c->protect(function () {
            return new ApcCache;
        });

        $c['orm.cache.factory.xcache'] = $c->protect(function () {
            return new XcacheCache;
        });

        $c['orm.cache.factory.filesystem'] = $c->protect(function ($options) {
            return $this->createFileSystemCache($options);
        });

        $c['orm.cache.factory.couchbase'] = $c->protect(function ($options) {
            return $this->createCouchDBCache($options);
        });
    }

    private function cacheLocator(Container $c, $name, $cacheName, $options)
    {
        $cacheNameKey = $cacheName . '_cache';

        if (!isset($options[$cacheNameKey])) {
            $options[$cacheNameKey] = $c['orm.default_cache'];
        }

        if (isset($options[$cacheNameKey]) && !is_array($options[$cacheNameKey])) {
            $options[$cacheNameKey] = array(
                'driver' => $options[$cacheNameKey],
            );
        }

        if (!isset($options[$cacheNameKey]['driver'])) {
            throw new \RuntimeException("No driver specified for '$cacheName'");
        }

        $driver = $options[$cacheNameKey]['driver'];

        $cacheInstanceKey = 'orm.cache.instances.' . $name . '.' . $cacheName;
        if (isset($c[$cacheInstanceKey])) {
            return $c[$cacheInstanceKey];
        }

        $cache = $c['orm.cache.factory']($driver, $options[$cacheNameKey]);

        if (isset($options['cache_namespace']) && $cache instanceof CacheProvider) {
            $cache->setNamespace($options['cache_namespace']);
        }

        return $c[$cacheInstanceKey] = $cache;
    }

    private function createMemcacheCache(Container $c, $options)
    {
        if (empty($options['host']) || empty($options['port'])) {
            throw new \RuntimeException('Host and port options need to be specified for memcache cache');
        }

        /** @var \Memcache $memcache */
        $memcache = $c['orm.cache.factory.backing_memcache']();
        $memcache->connect($options['host'], $options['port']);

        $cache = new MemcacheCache;
        $cache->setMemcache($memcache);

        return $cache;
    }

    private function createMemcachedCache(Container $c, $options)
    {
        if (empty($options['host']) || empty($options['port'])) {
            throw new \RuntimeException('Host and port options need to be specified for memcached cache');
        }

        /** @var \Memcached $memcached */
        $memcached = $c['orm.cache.factory.backing_memcached']();
        $memcached->addServer($options['host'], $options['port']);

        $cache = new MemcachedCache;
        $cache->setMemcached($memcached);

        return $cache;
    }

    private function createRedisCache(Container $c, $options)
    {
        if (empty($options['host']) || empty($options['port'])) {
            throw new \RuntimeException('Host and port options need to be specified for redis cache');
        }

        /** @var \Redis $redis */
        $redis = $c['orm.cache.factory.backing_redis']();
        $redis->connect($options['host'], $options['port']);

        if (isset($options['password'])) {
            $redis->auth($options['password']);
        }

        $cache = new RedisCache;
        $cache->setRedis($redis);

        return $cache;
    }

    private function createFileSystemCache($options)
    {
        if (empty($options['path'])) {
            throw new \RuntimeException('FilesystemCache path not defined');
        }

        $options += array(
            'extension' => FilesystemCache::EXTENSION,
            'umask'     => 0002,
        );

        return new FilesystemCache($options['path'], $options['extension'], $options['umask']);
    }

    private function createCouchDBCache($options)
    {
        $host = !empty($options['host']) ? $options['host'] : '127.0.0.1';
        $bucketName = !empty($options['bucket']) ? $options['bucket'] : 'default';
        $user = !empty($options['user']) ? $options['user'] : '';
        $password = !empty($options['password']) ? $options['password'] : '';

        $cache = new CouchbaseCache();
        $cache->setCouchbase(new \Couchbase($host, $user, $password, $bucketName));

        return $cache;
    }

    private function cacheFactory(Container $c, $driver, $options)
    {
        switch ($driver) {
            case 'array':
                return $c['orm.cache.factory.array']();
            case 'apc':
                return $c['orm.cache.factory.apc']();
            case 'xcache':
                return $c['orm.cache.factory.xcache']();
            case 'memcache':
                return $c['orm.cache.factory.memcache']($options);
            case 'memcached':
                return $c['orm.cache.factory.memcached']($options);
            case 'filesystem':
                return $c['orm.cache.factory.filesystem']($options);
            case 'redis':
                return $c['orm.cache.factory.redis']($options);
            case 'couchbase':
                return $c['orm.cache.factory.couchbase']($options);
            default:
                throw new \RuntimeException("Unsupported cache type '$driver' specified");
        }
    }
}
