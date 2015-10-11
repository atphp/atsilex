<?php

namespace atsilex\module\orm\tests;

use atsilex\module\system\tests\BaseTestCase;
use atsilex\module\system\tests\fixtures\modules\foo\models\FooEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class OrmModuleTest extends BaseTestCase
{
    public function testServices()
    {
        $app = $this->getApplication();
        $this->assertTrue($app->getEntityManager() instanceof EntityManagerInterface);
        $this->assertTrue($app->getEntityManager()->getRepository(FooEntity::class) instanceof EntityRepository);
    }
}
