<?php

namespace v3knet\module\system\controllers;

class HomeController extends BaseController
{

    public function get()
    {
        return $this->app->getTwig()->render('@system/pages/home.twig', [
            'content' => 'Welcome to <strong>Project Name</strong>!',
        ]);
    }

}
