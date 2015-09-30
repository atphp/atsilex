<?php

namespace atphp\module\drupal;

use atphp\module\App;
use atphp\module\drupal\controllers\DrupalController;
use atphp\module\drupal\drupal\Drupal;
use atsilex\module\Module;
use atsilex\module\system\ModularApp;
use Pimple\Container;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * @TODO On bootstrap Drupal, register custom error handler, let Silex's logger handle/log errors.
 * @TODO Run Drush from silex (php cli.php drush), custom hook implementations must be invoked.
 */
class DrupalModule extends Module
{

    protected $machineName = 'drupal';
    protected $name        = 'Drupal';
    protected $description = 'Provide integration to Drupal.';

    /**
     * {@inheritdoc}
     */
    public function register(Container $c)
    {
        $c['drupal'] = function (Container $c) {
            require_once __DIR__ . '/drupal/hooks.php';

            $drupal = new Drupal(
                $c['drupal.options']['root'],
                $c['drupal.options']['site_dir'],
                $c['drupal.options']['base_url'],
                $c['drupal.options']['global'],
                $c['drupal.options']['conf']
            );

            drilex_dispatcher($c['dispatcher']);
            drilex_drupal($drupal);

            $drupal
                ->setCache($c['cache'])
                ->setTwig($c['twig']);

            return $drupal;
        };
    }

    /**
     * {@inheritdoc}
     *
     * @param ModularApp $app
     */
    public function connect(Application $app)
    {
        /** @var ControllerCollection $route */
        $route = $app['controllers_factory'];

        $route
            ->match('/drupal', function () use ($app) {
                /** @var DrupalController $ctrl */
                $ctrl = $app['@drupal.ctrl.drupal'];
                $request = Request::create('/user');

                return $ctrl->action($request);
            })
            ->method('GET|POST');

        #$route->get('/user', '@drupal.ctrl.drupal:action');
        #$route->match('/user/login', '@drupal.ctrl.drupal:action')->method('GET|POST')->bind('user-login');
        #$route->get('/user/password', '@drupal.ctrl.drupal:action')->method('GET|POST')->bind('user-password');
        #$route->get('/user/logout', '@drupal.ctrl.drupal:actionGetLogout')->method('GET')->bind('user-logout');
        #$route->match('/user/{uid}/edit', '@drupal.ctrl.drupal:action')->method('GET|POST')->bind('user-edit');
        #$route->get('/{type}/{id}', '@drupal.ctrl.drupal:actionGetEntity')->bind('drupal-entity');

        return $route;
    }

}
