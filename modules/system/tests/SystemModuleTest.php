<?php

namespace v3knet\module\system\tests;

use Doctrine\Common\Cache\Cache;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Silex\Application;
use Silex\Controller;
use Silex\Provider\Locale\LocaleListener;
use Symfony\Component\Console\Application as Console;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ValidatorBuilderInterface;
use Twig_Environment;
use v3knet\module\system\commands\RunScriptCommand;
use v3knet\module\system\controllers\HomeController;
use v3knet\module\system\tests\fixtures\modules\foo\FooModule;
use v3knet\module\system\tests\fixtures\modules\foo\models\FooEntity;

class SystemModuleTest extends BaseTestCase
{

    public function testCoreServices()
    {
        $app = $this->getApplication();

        $this->assertTrue($app->getCache() instanceof Cache);
        $this->assertTrue($app->getLogger() instanceof LoggerInterface);
        $this->assertTrue($app->getValidatorBuilder() instanceof ValidatorBuilderInterface);
        $this->assertTrue($app->getValidator() instanceof ValidatorInterface);
        $this->assertTrue($app->getFormFactory() instanceof FormFactoryInterface);
        $this->assertTrue($app['locale.listener'] instanceof LocaleListener);
        $this->assertTrue($app->getTranslator() instanceof Translator);
        $this->assertTrue($app['@system.ctrl.home'] instanceof HomeController); # ServiceControllerServiceProvider
        $this->assertTrue($app->getSession() instanceof Session);
        $this->assertTrue($app->getSerializer() instanceof SerializerInterface);
        $this->assertTrue($app->getConsole() instanceof Console);
        $this->assertTrue($app->getTwig() instanceof Twig_Environment);
    }

    public function testDoctrineServices()
    {
        $app = $this->getApplication();
        $this->assertTrue($app->getDb() instanceof Connection);
        $this->assertTrue($app->getEntityManager() instanceof EntityManagerInterface);
        $this->assertTrue($app->getEntityManager()->getRepository(FooEntity::class) instanceof EntityRepository);
    }

    public function testTwigService()
    {
        $app = $this->getApplication();
        $this->assertTrue($app['twig.loader']->exists('@system/pages/home.twig'));
    }

    public function testMagicServices()
    {
        $app = $this->getApplication();
        $this->assertTrue($app['@system.cmd.run_script'] instanceof RunScriptCommand);
        $this->assertTrue($app['@system.ctrl.home'] instanceof HomeController);
    }

    public function testModule()
    {
        $app = $this->getApplication();

        $this->assertTrue($app->isModuleExists('foo'));
        $this->assertTrue($app->getModule('foo') instanceof FooModule);
    }

    /**
     * Make sure WebProfilerServiceProvider is registered & booted.
     */
    public function testWebProfiler()
    {
        $app = $this->getApplication();

        $this->assertTrue($app['controllers']->match('/_profiler/search') instanceof Controller);
    }

}
