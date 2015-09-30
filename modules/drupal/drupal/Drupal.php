<?php

namespace atphp\module\drupal\drupal;

use Doctrine\Common\Cache\Cache;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use atphp\module\drupal\drupal\api\DrupalApiTrait;

class Drupal
{

    use BootTrait;
    use DrupalApiTrait;

    /** @var Cache */
    private $cache;

    /** @var \Twig_Environment */
    private $twig;

    /** @var string */
    private $root;

    /** @var string */
    private $baseUrl;

    /** @var string */
    private $siteDir;

    /** @var array[] */
    private $global;

    /** @var mixed[] */
    private $conf;

    public function __construct($root, $siteDir, $baseUrl, array $global = [], array $conf = [])
    {
        $this->root = $root;
        $this->siteDir = $siteDir;
        $this->baseUrl = $baseUrl;
        $this->global = $global;
        $this->conf = $conf;

        $_SERVER['REMOTE_ADDR'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : pathinfo($baseUrl)['basename'];
        $_SERVER['HTTP_HOST'] = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : pathinfo($baseUrl)['basename'];
        $_SERVER['REQUEST_URI'] = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
    }

    /**
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param Cache $cache
     * @return self
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * @param \Twig_Environment $twig
     * @return self
     */
    public function setTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;
        return $this;
    }

    /**
     * @return \Twig_Environment
     */
    public function getTwig()
    {
        return $this->twig;
    }


    public function handle(Request $request)
    {
        $path = trim($request->getRequestUri(), '/') ?: '<front>';
        $output = menu_execute_active_handler($path, false);

        // @TODO: Remove set cookie header.
        // @TODO: Get correct response code

        switch ($output) {
            case MENU_NOT_FOUND:
                throw new NotFoundHttpException();
            case MENU_ACCESS_DENIED:
                throw new AccessDeniedException();
            default:
                return $this->convertToDrupalResponse($output);
        }
    }

    public function convertToDrupalResponse($output, $code = 200)
    {
        $title = drupal_get_title();
        $messages = drupal_get_messages() ?: [];
        $css = array_map(function ($item) {
            return ['preprocess' => false] + $item;
        }, drupal_add_css());

        return new Response(
            $title,
            is_string($output) ? $output : drupal_render($output),
            drupal_get_css($css),
            [
                'header' => drupal_get_js(),
                'footer' => drupal_get_js('footer')
            ],
            $code,
            $messages
        );
    }

    public function getUser()
    {
        return isset($GLOBALS['user']) ? $GLOBALS['user'] : null;
    }

}
