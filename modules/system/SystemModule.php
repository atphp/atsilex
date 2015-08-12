<?php

namespace v3knet\module\system;

use Pimple\Container;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use v3knet\module\Module;
use v3knet\module\system\providers\Register;

/**
 * @TODO Include Swift Mailer.
 * @TODO Support HAL
 *      - https://github.com/mikekelly/hal-browser
 *      - Nocarrier\Hal — https://github.com/blongden/hal
 *      - https://github.com/easybiblabs/silex-hal-provider
 *      - http://www.slideshare.net/LukeStokes/pox-to-hateoas-13077649
 * @TODO Support https://vadikom.github.io/smartmenus/src/demo/bootstrap-navbar.html?
 * @TODO Support drupal navbar?
 */
class SystemModule extends Module
{

    protected $machineName = 'system';
    protected $name        = 'System Module';
    protected $description = 'Implements core functions.';

    public function register(Container $c)
    {
        $isModular = $c instanceof ModularApp;
        (new Register($isModular))->register($c);

        // Site front-page
        if (isset($c['site_frontpage']) && ('/' !== $c['site_frontpage'])) {
            $c->get('/', function (Container $c) {
                return $c->handle(Request::create($c['site_frontpage']));
            });
        }
    }

    public function connect(Application $app)
    {
        /** @var ControllerCollection $route */
        $route = $app['controllers_factory'];

        $route->get('/hello', '@system.ctrl.home:get')->bind('hello');
        $route->get('/login', '@system.ctrl.user:getLogin')->bind('user-login');
        $route->get('/logout', '@system.ctrl.user:getLogout')->bind('user-logout');

        return $route;
    }

}
