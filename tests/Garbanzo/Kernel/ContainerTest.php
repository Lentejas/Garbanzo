<?php
namespace Test\Garbanzo\Kernel;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Test;
use Garbanzo\Kernel\Container;
use Garbanzo\Kernel\Interfaces\PluginInterface;
use Garbanzo\Kernel\PluginLoader;
use StdClass;

class ContainerTest extends TestCase {

    protected $loaderMock;

    public function setUp() {
        $this->loaderMock = $this->prophesize(PluginLoader::class);
    }

    /**
     * @test
     */
    public function itCanAddAMiddlewareAndAccessIt() {
        $container = new Container($this->loaderMock->reveal());
        $returnValue = 'FooBar';
        $container->addMiddleware(function() use ($returnValue) {
            return $returnValue;
        });
        $middlewares = $container->getMiddlewares();
        $this->assertCount(1, $middlewares);
        $this->assertEquals($returnValue, $middlewares[0]());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function itCanNotAddAMiddlewareNotCallable() {
        $container = new Container($this->loaderMock->reveal());
        $container->addMiddleware('FooBar');
    }

    /**
     * @test
     */
    public function itCanAddAServiceAndAccessIt() {
        $container = new Container($this->loaderMock->reveal());
        $name = 'Foo';
        $returnValue = 'Bar';
        $container->addService($name, function() use ($returnValue) {
            return $returnValue;
        });
        $services = $container->getServices();
        $this->assertCount(1, $services);
        $this->assertArrayHasKey($name, $services);
        $this->assertEquals($returnValue, $services[$name]());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function itCanNotAddAServiceNotCallable() {
        $container = new Container($this->loaderMock->reveal());
        $container->addservice('Foo', 'Bar');
    }

    /**
     * @test
     * @expectedException Exception
     */
   public function itCanNotAddAServiceWithTheSameName() {
       $container = new Container($this->loaderMock->reveal());
       $name = 'Foo';
       $returnValue = 'Bar';
       $container->addService($name, function() use ($returnValue) {
           return $returnValue;
       });
       $container->addService($name, function() use ($returnValue) {
           return $returnValue;
       });
   }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function itCanNotAddAServiceNameNotString() {
        $container = new Container($this->loaderMock->reveal());
        $container->addservice(function() {}, 'FooBar');
    }

    /**
     * @test
     */
    public function itCanAddAPluginAndAccessIt() {
        $this->markTestSkipped('NotImplemented');
        $name = 'Foo';
        $container = new Container($this->loaderMock->reveal());
        $pluginMock = $this->prophesize(PluginInterface::class);
        $pluginMock->getName()->shouldBeCalled()->willReturn($name);
        $plugin = $pluginMock->reveal();
        $container->addPlugin($plugin);
        $this->assertEquals($plugin, $container->getPlugin($name));
    }


    /**
     * @test
     * @expectedException TypeError
     */
    public function itCanNotAddAPlugginWhichDoesNotImplementsTheInterface() {
        $this->markTestSkipped('NotImplemented');
        $container = new Container($this->loaderMock->reveal());
        $container->addPlugin(new stdClass());
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function itCanNotAddTwoPluginWithTheSameName() {
        $this->markTestSkipped('NotImplemented');
        $name = 'Foo';
        $container = new Container($this->loaderMock->reveal());
        $pluginMock = $this->prophesize(PluginInterface::class);
        $pluginMock->getName()->shouldBeCalled()->willReturn($name);
        $plugin = $pluginMock->reveal();
        $container->addPlugin($plugin);
        $container->addPlugin($plugin);
    }


}
