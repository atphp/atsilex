<?php

namespace atsilex\module\system\traits;

use Composer\Autoload\ClassLoader;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;

trait V3kAppTrait
{
    /** @var  ClassLoader */
    private $classLoader;

    /**
     * @return ClassLoader
     */
    public function getClassLoader()
    {
        return $this->classLoader;
    }

    /**
     * @param ClassLoader $loader
     * @return self
     */
    public function setClassLoader(ClassLoader $loader = null)
    {
        if (null !== $loader) {
            $this->classLoader = $loader;

            $dir = $this->getAppRoot() . '/files/vendor/composer';
            if (is_dir($dir)) {
                $this->registerExtraClassLoading($dir);
            }
        }

        return $this;
    }

    private function registerExtraClassLoading($dir)
    {
        foreach (require $dir . '/autoload_namespaces.php' as $namespace => $path) {
            $this->classLoader->set($namespace, $path);
        }

        foreach (require $dir . '/autoload_psr4.php' as $namespace => $path) {
            $this->classLoader->setPsr4($namespace, $path);
        }

        if ($classMap = require $dir . '/autoload_classmap.php') {
            $this->classLoader->addClassMap($classMap);
        }
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->getSession()->getFlashBag()->all();
    }

    /**
     * @param string $message
     * @param string $type
     * @return self
     */
    public function addMessage($message, $type = 'success')
    {
        $this->getSession()->getFlashBag()->add($type, $message);

        return $this;
    }

    public function onBefore(Request $request)
    {
        // Convert json body to array structure
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            // $data = json_decode($request->getContent(), true);
            // $request->request->replace(is_array($data) ? $data : array());
        }
    }

    /**
     * Custom error handler page, it's must be placed before any route definition.
     * Customize error template by error code by create template: pages/error/{404,500,..}.twig
     *
     * @param \Exception $e
     * @return string
     */
    public function onError(\Exception $e)
    {
        $template = 'pages/error/default.twig';
        $exception = FlattenException::create($e);
        $code = $exception->getStatusCode();
        $handler = new ExceptionHandler($this['debug']);
        if ($this->getTwig()->getLoader()->exists("pages/error/$code.twig")) {
            $template = "pages/error/$code.twig";
        }
        return $this->getTwig()->render($template, [
            'exception'  => $exception,
            'stylesheet' => $handler->getStylesheet($exception),
            'content'    => $handler->getContent($exception),
        ]);
    }
}
