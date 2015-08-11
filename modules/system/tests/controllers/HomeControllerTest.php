<?php

namespace v3knet\module\system\tests\controllers;

use Symfony\Component\HttpFoundation\Request;
use v3knet\module\system\tests\BaseTestCase;

class HomeControllerTest extends BaseTestCase
{

    public function testActionGet()
    {
        $app = $this->getApplication();
        $response = $app->handle(Request::create('/hello'));
        $this->assertContains('Welcome to <strong>Project Name</strong>!', $response->getContent());
    }

}
