<?php
namespace Test\Garbanzo\Kernel;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Test;
use Garbanzo\Kernel\Container;
use Garbanzo\Kernel\Interfaces\PluginInterface;
use StdClass;

class ContainerTest extends TestCase {

    /**
     * @test
     */
    public function itCanAddAMiddlewareAndAccessIt() {
        $container = new Container();
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
        $container = new Container();
        $container->addMiddleware('FooBar');
    }

    /**
     * @test
     */
    public function itCanAddAServiceAndAccessIt() {
        $container = new Container();
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
        $container = new Container();
        $container->addservice('Foo', 'Bar');
    }

    /**
     * @test
     * @expectedException Exception
     */
   public function itCanNotAddAServiceWithTheSameName() {
       $container = new Container();
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
        $container = new Container();
        $container->addservice(function() {}, 'FooBar');
    }

    /**
     * @test
     */
    public function itCanAddAPluginAndAccessIt() {
        $name = 'Foo';
        $container = new Container();
        $pluginMock = $this->prophesize(PluginInterface::class);
        $pluginMock->getName()->shouldBeCalled()->willReturn($name);
        $plugin = $pluginMock->reveal();
        $container->addPlugin($plugin);
        $plugins = $container->getPlugins();
        $this->assertCount(1, $plugins);
        $this->assertArrayHasKey($name, $plugins);
        $this->assertEquals($plugin, $plugins[$name]);
        $this->assertEquals($plugin, $container->getPlugin($name));
    }


    /**
     * @test
     * @expectedException TypeError
     */
    public function itCanNotAddAPlugginWhichDoesNotImplementsTheInterface() {
        $container = new Container();
        $container->addPlugin(new stdClass());
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function itCanNotAddTwoPluginWithTheSameName() {
        $name = 'Foo';
        $container = new Container();
        $pluginMock = $this->prophesize(PluginInterface::class);
        $pluginMock->getName()->shouldBeCalled()->willReturn($name);
        $plugin = $pluginMock->reveal();
        $container->addPlugin($plugin);
        $container->addPlugin($plugin);
    }


}
