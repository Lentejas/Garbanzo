<?php
namespace Test\Garbanzo\Kernel;

use PHPUnit\Framework\TestCase;
use Garbanzo\Kernel\PluginLoader;
use Garbanzo\Kernel\Configuration;
use StdClass;

class PluginLoaderTest extends TestCase {

    protected $configurationMock;

    public function setUp() {
        $pluginsList = new StdClass();
        $pluginsList->core = "test.json";
        $pluginsList->core1 = "test1.json";
        $mock = $this->prophesize(Configuration::class);
        $mock->getProperties()->shouldBeCalled()->willReturn($pluginsList);
        $this->configurationMock = $mock->reveal();
    }

    public function testRegister() {
        $loader = new PluginLoader($this->configurationMock);
        $expected = array(
            'core' => 'test.json',
            'core1' => 'test1.json',
        );
        $this->assertEquals($expected, $loader->getRegistered());
    }
}
