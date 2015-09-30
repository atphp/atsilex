<?php

namespace atphp\module\drupal\drupal\api;

class CacheAPI
{

    public function get($cacheId, $cacheBin = 'cache')
    {
        return cache_get($cacheId, $cacheBin);
    }

    public function getMultiple(array &$cacheIds, $cacheBin = 'cache')
    {
        return cache_get_multiple($cacheIds, $cacheBin);
    }

    public function set($cid, $data, $bin = 'cache', $expire = CACHE_PERMANENT)
    {
        return cache_set($cid, $data, $bin, $expire);
    }

    public function flush($cid = null, $bin = null, $wildcard = false)
    {
        return cache_clear_all($cid, $bin, $wildcard);
    }

    public function isEmpty($bin)
    {
        return cache_is_empty($bin);
    }

    function flushAll()
    {
        return drupal_flush_all_caches();
    }

}
