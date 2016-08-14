<?php
namespace Garbanzo\Core\Services;

use Garbanzo\Kernel\Interfaces\ContainerInterface;
use Garbanzo\Kernel\App;
use Garbanzo\Kernel\Configuration;

class Router {

    protected $container;
    private $routes = array();

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function addRoute($routeConfig) {
        $prefix = $routeConfig->prefix;
        $routeConfig = $this->loadRouteConfiguration($routeConfig->file)->getProperties();
        //print_r($routeConfig);
        foreach ($routeConfig as $route => $config) {
            $this->computeRoute($route,$config,$prefix);
        }
    }

    protected function loadRouteConfiguration($configFilePath){
        if (! file_exists(Configuration::$ROOT . $configFilePath)) {
            throw new Exception('The file ' . $configFilePath . ' was not found.');
        }
        $routeConfiguration = new Configuration(App::getEnvironment());
        $routeConfiguration->setConfigRootDirectory('/');
        $routeConfiguration->loadFile($configFilePath);
        return $routeConfiguration;
    }

    protected function computeRoute($route,$config,$prefix){
        $r = array();
        $r['class'] = $config->controller;
        $r['function'] = $config->controller_method;
        preg_match_all("#:[a-z]+#",$config->pattern,$params);
        var_dump(count($params));
        $this->routes[$route] = $r;
    }

    public function route($url){

    }
}
