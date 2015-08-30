<?php

namespace atsilex\module\system\tests;

use atsilex\module\system\commands\RunScriptCommand;
use atsilex\module\system\controllers\HomeController;
use atsilex\module\system\tests\fixtures\modules\foo\FooModule;
use atsilex\module\system\tests\fixtures\modules\foo\models\FooEntity;
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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ValidatorBuilderInterface;
use Twig_Environment;

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
        $this->assertTrue($app['system.ctrl.home'] instanceof HomeController); # ServiceControllerServiceProvider
        $this->assertTrue($app->getSession() instanceof Session);
        $this->assertTrue($app->getSerializer() instanceof SerializerInterface);
        $this->assertTrue($app->getConsole() instanceof Console);
        $this->assertTrue($app->getTwig() instanceof Twig_Environment);
        $this->assertTrue($app->getMailer() instanceof \Swift_Mailer);
        $this->assertTrue($app->getMailerTransport() instanceof \Swift_Transport);
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
        $this->assertTrue($app['system.cmd.run_script'] instanceof RunScriptCommand);
        $this->assertTrue($app['system.ctrl.home'] instanceof HomeController);
    }

    public function testModule()
    {
        $app = $this->getApplication();

        $this->assertTrue($app->isModuleExists('foo'));
        $this->assertTrue($app->getModule('foo') instanceof FooModule);
    }

    public function testModuleYamlRouting()
    {
        $app = $this->getApplication();

        $request = Request::create('/hello-yaml');
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains($response->getContent(), 'Hi there!');
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
