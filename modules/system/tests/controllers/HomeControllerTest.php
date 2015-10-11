<?php

namespace atsilex\module\system\tests\controllers;

use atsilex\module\system\tests\BaseTestCase;
use Symfony\Component\HttpFoundation\Request;

class HomeControllerTest extends BaseTestCase
{
    public function testActionGet()
    {
        $app = $this->getApplication();
        $response = $app->handle(Request::create('/hello'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Welcome to <strong>Project Name</strong>!', $response->getContent());
    }
}
