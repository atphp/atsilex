<?php

namespace atphp\module\drupal\drupal;

class Response
{

    private $title;
    private $content;
    private $code;
    private $messages;
    private $css;
    private $js;
    private $baseUrl;

    public function __construct($title, $content, $css, array $js = [], $code = 200, array $messages = [])
    {
        $this->title = $title;
        $this->content = $content;
        $this->css = str_replace($this->getBaseUrl(), '%base_url%', $css);
        $this->js = str_replace($this->getBaseUrl(), '%base_url%', $js);
        $this->code = $code;
        $this->messages = $messages;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @return mixed
     */
    public function getCss()
    {
        return $this->css;
    }

    /**
     * @param mixed $css
     * @return self
     */
    public function setCss($css)
    {
        $this->css = $css;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getJs($scope = 'header')
    {
        return $this->js[$scope];
    }

    /**
     * @param mixed $js
     * @return self
     */
    public function setJs($js)
    {
        $this->js = $js;
        return $this;
    }

    public function getBaseUrl()
    {
        global $base_url;

        return $base_url;
    }

}
