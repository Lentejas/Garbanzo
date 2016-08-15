<?php
namespace Garbanzo\Kernel;

use Garbanzo\Kernel\Container;
use InvalidArgumentException;

class App {

    const PROD = 'prod';
    const DEV = 'dev';
    const TEST = 'test';

    private static $environment;
    private $container;
    private $configurationManager;

    public function __construct($environment = self::PROD) {
        self::$environment = $environment;
        $this->configurationManager = new ConfigurationManager($_SERVER['DOCUMENT_ROOT'] . '/..');
        $loader = new PluginLoader($this->configurationManager);
        $this->container = new Container($loader, new ServiceManager($this->configurationManager));
        $this->container->loadPlugin('garbanzo-core');
        $routes = $this->container->get('config')->getConfigurationFile('routes.json')->getProperties();
        $this->container->get('router')->addRoutes($routes);
    }

    public function run() {
        $request = $this->container->get('http')->getRequest();
        $route = $this->container->get('router')->route($request);
        $response = $this->container->get('manager.controller')->call($route);
        $this->container->get('http')->send($response);
    }

    public function getConfiguration() {
        return $this->configuration;
    }

    public static function getEnvironment() {
        return self::$environment;
    }

    public static function setEnvironment($environment) {
        if (($environment !== self::PROD)
            && ($environment !== self::DEV)
            && ($environment !== self::TEST)) {
            throw new InvalidArgumentException;
        }
        self::$environment = $environment;
    }
}
