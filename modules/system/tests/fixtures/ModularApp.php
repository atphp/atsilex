<?php

namespace v3knet\module\system\tests\fixtures;

use Silex\Application;
use v3knet\module\system\traits\GetterAppTrait;
use v3knet\module\system\traits\ModularAppTrait;

class ModularApp extends Application
{

    use ModularAppTrait;
    use GetterAppTrait;

}
