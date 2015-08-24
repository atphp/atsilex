<?php

namespace atsilex\module\system\tests\fixtures\modules\foo;

use atsilex\module\Module;

class FooModule extends Module
{

    protected $machineName = 'foo';
    protected $name        = 'Foo Module';
    protected $routeFile   = true; // '%dir/resources/config/routing.yml'

}
