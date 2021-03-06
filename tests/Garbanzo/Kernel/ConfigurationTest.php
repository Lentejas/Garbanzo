<?php
namespace Test\Garbanzo\Kernel;

use PHPUnit\Framework\TestCase;
use Garbanzo\Kernel\Configuration;
use Garbanzo\Kernel\App;
use StdClass;

class ConfigurationTest extends TestCase {

    /**
     * @test
     */
    public function itCanLoadADefaultProductionConfigFile() {
        $configuration = new Configuration(__DIR__ . '/../../..');
        $configuration->loadFile('plugins.json');
        $this->assertNotNull($configuration->get('core'));
    }

    /**
     * @test
     * @depends itCanLoadADefaultProductionConfigFile
     */
    public function itCanLoadACustomProductionConfigFile() {
        $configuration = new Configuration(__DIR__ . '/../../..');
        $configuration->setConfigRootDirectory('/tests/config/');
        $configuration->loadFile('test.json');
        $expected = new StdClass();
        $expected->prop1 = 'VALUE1';
        $expected->prop4 = 'VALUE4';
        $this->assertEquals($expected, $configuration->get('test1'));
        $this->assertEquals("Val3", $configuration->get('test3'));
        $this->assertEquals('prod', $configuration->get('tempered'));
    }

    /**
     * @test
     * @depends itCanLoadADefaultProductionConfigFile
     */
    public function itCanGetAMultiPartProperty() {
        $configuration = new Configuration(__DIR__ . '/../../..');
        $configuration->setConfigRootDirectory('/tests/config/');
        $configuration->loadFile('test.json');
        $this->assertEquals('VALUE1', $configuration->get('test1.prop1'));
    }

    /**
     * @test
     * @depends itCanLoadACustomProductionConfigFile
     */
    public function itCanLoadADevelopmentConfigFile() {
        App::setEnvironment(App::DEV);
        $configuration = new Configuration(__DIR__ . '/../../..');
        $configuration->setConfigRootDirectory('/tests/config/');
        $configuration->loadFile('test.json');
        $this->assertEquals('VALUE1', $configuration->get('test1.prop1'));
        $this->assertEquals('VALUE2', $configuration->get('test1.prop2'));
        $this->assertEquals('Val', $configuration->get('test2'));
    }

    /**
     * @test
     * @depends itCanLoadACustomProductionConfigFile
     */
    public function itCanConcatADevAndAProductionConfigFile() {
        App::setEnvironment(App::DEV);
        $configuration = new Configuration(__DIR__ . '/../../..');
        $configuration->setConfigRootDirectory('/tests/config/');
        $configuration->loadFile('test.json');
        $this->assertEquals('VALUE1', $configuration->get('test1.prop1'));
        $this->assertEquals('VALUE2', $configuration->get('test1.prop2'));
        $this->assertEquals('VALUE4', $configuration->get('test1.prop4'));
        $this->assertEquals('Val', $configuration->get('test2'));
        $this->assertEquals('Val3', $configuration->get('test3'));
        $this->assertEquals('dev', $configuration->get('tempered'));
    }

}
