<?php

namespace atphp\module\drupal\controllers;

use atphp\module\drupal\drupal\Drupal;
use atsilex\module\system\controllers\BaseController;
use atsilex\module\system\ModularApp;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DrupalController extends BaseController
{

    /** @var Drupal */
    protected $drupal;

    /** @var  string */
    private $template = '@drupal/pages/index.twig';

    public function __construct(ModularApp $c)
    {
        parent::__construct($c);

        $this->drupal = $c['drupal'];

        $preHeaders = headers_list();
        $this->drupal->boot();

        foreach (headers_list() as $header) {
            if (!in_array($header, $preHeaders)) {
                list($name,) = explode(': ', $header, 2);
                header_remove($name);
            }
        }
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function action(Request $request)
    {
        return $this->drupal->getTwig()->render($this->template, [
            'response' => $this->drupal->handle($request)
        ]);
    }

    public function actionGetLogout()
    {
        $return = $this->action(Request::create('user/logout'));
        session_destroy();

        return $return;
    }

    public function actionMatchUserEdit($uid)
    {
        return $this->action(Request::create("/user/$uid/edit"));
    }

    public function actionGetEntity($type, $id)
    {
        if (!$entity = entity_load_single($type, $id)) {
            throw new NotFoundHttpException();
        }

        $output = entity_view($type, [$entity]);

        return $this->drupal->getTwig()->render($this->template, [
            'response' => $this->drupal->convertToDrupalResponse($output)
        ]);
    }

}
