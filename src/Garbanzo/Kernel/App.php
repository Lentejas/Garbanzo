<?php
namespace Garbanzo\Kernel;

use Garbanzo\Core\HTTP\Response;
use Garbanzo\Kernel\Container;
use Garbanzo\Theme\Theme;
use InvalidArgumentException;

class App {

    const PROD = 'prod';
    const DEV = 'dev';
    const TEST = 'test';

    private static $environment;
    /** @var Container  */
    private $container;
    private $configurationManager;

    public function __construct($environment = self::PROD) {
        self::$environment = $environment;
        $this->configurationManager = new ConfigurationManager($_SERVER['DOCUMENT_ROOT'] . '/..');
//        $this->configurationManager->loadFile('plugins.json');
        $loader = new PluginLoader($this->configurationManager);
        $this->container = new Container($loader, new ServiceManager($this->configurationManager));
        $this->container->loadPlugin('garbanzo-core');
        $routes = $this->container->get('config')->getConfigurationFile('routes.json')->getProperties();
        $this->container->get('router')->addRoutes($routes);
    }

    public function run() {
        try {
            $request = $this->container->get('http')->getRequest();
            $route = $this->container->get('router')->route($request);

            $this->container->loadPlugin('garbanzo-cms');
            $this->container->loadPlugin('db');
            $this->container->loadPlugin('theme');
            /** @var Theme $theme */
            $theme = $this->container->get('theme.theme');

            $layout = $theme->getLayout();

            $layout->setData($this->container->get('manager.controller')->call($route));

            $this->container->get('http')->send($layout->createResponse());


        } catch (\Exception $e) {
            $this->container->get('http')->send(new Response($response));
        }

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
