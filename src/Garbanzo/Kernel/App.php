<?php
namespace Garbanzo\Kernel;

use Garbanzo\Kernel\Container;

class App {

    const PROD = 'prod';
    const DEV = 'dev';
    const TEST = 'test';

    private static $environment;
    private $container;
    private $configuration;

    public function __construct($environment = self::PROD) {
        self::$environment = $environment;
        $this->configuration = new Configuration($_SERVER['DOCUMENT_ROOT'] . '/..');
        $this->configuration->loadFile('plugins.json');
        $loader = new PluginLoader($this->configuration);
        $this->container = new Container($loader);
        $this->container->loadPlugin('core');
    }

    public function run() {
        $this->container->get('garbanzo-core.logger')->crudeLog('running');
    }

    public function getConfiguration() {
        return $this->configuration;
    }

    public static function getEnvironment() {
        return self::$environment;
    }
}
