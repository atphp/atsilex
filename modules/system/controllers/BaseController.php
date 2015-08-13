<?php

namespace atsilex\module\system\controllers;

use atsilex\module\App;
use atsilex\module\system\ModularApp;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

abstract class BaseController
{

    /** @var ModularApp */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return App
     */
    protected function getApp()
    {
        return $this->app;
    }

    protected function getEM()
    {
        return $this->getApp()->getEntityManager();
    }

    protected function getRepository($name)
    {
        return $this->getEM()->getRepository($name);
    }

    protected function createEntityFromRequest(Request $request, $type)
    {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $json = $request->getContent();

            return $this->getApp()->getSerializer()->deserialize($json, $type, 'json');
        }
    }

    protected function saveEntity(&$entity)
    {
        $em = $this->getEM();
        $em->persist($entity);

        return $em->flush();
    }

    protected function json($data, $status = 200, array $header = [])
    {
        $output = $this->getApp()->getSerializer()->toArray($data);

        return $this->getApp()->json($output, $status, $header);
    }

}
