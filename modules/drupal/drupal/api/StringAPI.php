<?php

namespace atphp\module\drupal\drupal\api;

class StringAPI
{

    public function drupalImplodeTags($tags)
    {
        return drupal_implode_tags($tags);
    }

    public function drupalJsonDecode($var)
    {
        return drupal_json_decode($var);
    }

    public function drupalJsonEncode($var)
    {
        return drupal_json_encode($var);
    }

    public function drupalJsonOutput($var = null)
    {
        return drupal_json_output($var = null);
    }

    public function validEmailAddress($mail)
    {
        return valid_email_address($mail);
    }

}
