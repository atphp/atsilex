<?php

namespace atphp\module\drupal\drupal\api;

class PathAPI
{

    public function base_path()
    {
        return base_path();
    }

    public function check_url($uri)
    {
        return check_url($uri);
    }

    public function l($text, $path, array $options = [])
    {
        return l($text, $path, $options);
    }

    public function url($path = null, array $options = [])
    {
        return url($path, $options);
    }

    public function url_is_external($path)
    {
        return url_is_external($path);
    }

    public function valid_url($url, $absolute = false)
    {
        return valid_url($url, $absolute);
    }

    function drupal_encode_path($path) {}
    function drupal_get_path($type, $name) {}

}
