<?php

namespace atsilex\module\tests\fixtures\foo;

use atsilex\module\Module;

class FooModule extends Module
{
    protected $routePrefix = '/';
    protected $machineName = 'foo';
    protected $name        = 'Foo Module';
    protected $description = 'The foo module, just for test cases.';
    protected $version     = '0.1.0';
}
