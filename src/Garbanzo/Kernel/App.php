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
        $this->configuration = new Configuration(self::$environment, $_SERVER['DOCUMENT_ROOT'] . '/..');
        $this->configuration->loadFile('plugins.json');
        $loader = new PluginLoader($this->configuration);
        $this->container = new Container($loader);
        $this->container->loadPlugin('core');
    }

    public function run() {
        $this->container->get('garbanzo-core.logger')->crudeLog('running');
        $routes = new Configuration(self::$environment, $_SERVER['DOCUMENT_ROOT'] . '/..');
        $routes->loadFile('routes.json');
        echo "<pre>";
        foreach ($routes->getProperties() as $route) {
            $this->container->get('garbanzo-core.router')->addRoute($route);
        }
    }

    public function getConfiguration() {
        return $this->configuration;
    }

    public static function getEnvironment() {
        return self::$environment;
    }
}
