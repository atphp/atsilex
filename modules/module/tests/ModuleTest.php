<?php

namespace atsilex\module\tests;

use atsilex\module\tests\fixtures\foo\FooModule;
use atsilex\module\tests\fixtures\InvalidModule;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Machine name is required: atsilex\module\tests\fixtures\InvalidModule
     */
    public function testInvalidModule()
    {
        $invalid = new InvalidModule();
    }

    public function testFooModule()
    {
        $module = new FooModule();
        $this->assertEquals('/', $module->getRoutePrefix());
        $this->assertEquals('foo', $module->getMachineName());
        $this->assertEquals('Foo Module', $module->getName());
        $this->assertEquals('The foo module, just for test cases.', $module->getDescription());
        $this->assertEquals('0.1.0', $module->getVersion());
        $this->assertEquals(__NAMESPACE__ . '\fixtures\foo', $module->getNamespace());
    }

    public function testEntityMapping()
    {
        $this->assertEquals(
            [
                [
                    'type'      => 'annotation',
                    'namespace' => __NAMESPACE__ . '\fixtures\foo\models',
                    'path'      => __DIR__ . '/fixtures/foo/models',
                ]
            ],
            (new FooModule())->getEntityMappings()
        );
    }

    /**
     * @dataProvider dataMagicServiceClass
     */
    public function testMagicServiceClass($expect, $actual)
    {
        $this->assertEquals($expect, $actual);
    }

    public function dataMagicServiceClass()
    {
        $module = new FooModule();

        return [
            [
                __NAMESPACE__ . '\fixtures\foo\controllers\HelloController',
                $module->getMagicServiceClass('ctrl', 'hello')
            ],
            [
                __NAMESPACE__ . '\fixtures\foo\controllers\hello\ThereController',
                $module->getMagicServiceClass('ctrl', 'hello.there')
            ],
            [
                __NAMESPACE__ . '\fixtures\foo\commands\HelloCommand',
                $module->getMagicServiceClass('cmd', 'hello')
            ],
            [
                __NAMESPACE__ . '\fixtures\foo\commands\hello\ThereCommand',
                $module->getMagicServiceClass('cmd', 'hello.there')
            ],
        ];
    }
}
