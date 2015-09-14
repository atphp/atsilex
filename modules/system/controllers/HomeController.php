<?php

namespace atsilex\module\system\controllers;

class HomeController extends BaseController
{
    public function actionGet()
    {
        return $this->app->getTwig()->render('@system/pages/home.twig', [
            'content' => 'Welcome to <strong>Project Name</strong>!',
        ]);
    }
}
