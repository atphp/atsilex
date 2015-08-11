<?php

namespace v3knet\module\swagger;

use Pimple\Container;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\Routing\RouteCollection;
use v3knet\module\Module;
use v3knet\module\swagger\controllers\OptionsController;

class SwaggerModule extends Module
{

    protected $machineName = 'swagger';
    protected $name        = 'Swagger Module';

    public function register(Container $c)
    {
        $c['cors.allowOrigin'] = '*'; // Defaults to all
        $c['cors.allowMethods'] = null; // Defaults to all
        $c['cors.maxAge'] = null;
        $c['cors.allowCredentials'] = null;
        $c['cors.exposeHeaders'] = null;
        $c['cors'] = $c->protect(new Cors($c));
    }

    public function connect(Application $app)
    {
        /** @var ControllerCollection $route */
        $route = $app['controllers_factory'];

        $route
            ->get('/swagger.json', '@swagger.ctrl.swagger:actionGet')
            ->after($app['cors'])
            ->bind('swagger-json');

        $route
            ->get('/swagger', '@swagger.ctrl.swagger:actionGetUI');

        return $route;
    }

    /**
     * Add OPTIONS method support for all routes.
     *
     * @param Application $app
     */
    public function boot(Application $app)
    {
        foreach ($this->findAllowedMethods($app['routes']) as $path => $route) {
            $app
                ->match($path, new OptionsController($route['methods']))
                ->setRequirements($route['requirements'])
                ->method('OPTIONS');
        }
    }

    private function findAllowedMethods(RouteCollection $routes)
    {
        $allow = [];

        foreach ($routes as $route) {
            $path = $route->getPath();
            if (!array_key_exists($path, $allow)) {
                $allow[$path] = ['methods' => [], 'requirements' => []];
            }
            $requirements = $route->getRequirements();
            unset($requirements['_method']);
            $allow[$path]['methods'] = array_merge($allow[$path]['methods'], $route->getMethods());
            $allow[$path]['requirements'] = array_merge($allow[$path]['requirements'], $requirements);
        }

        return $allow;
    }

}
