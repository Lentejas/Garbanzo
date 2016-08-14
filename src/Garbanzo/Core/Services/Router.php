<?php
namespace Garbanzo\Core\Services;

use Garbanzo\Kernel\Traits\ServiceCreation;
use Garbanzo\Kernel\App;
use Garbanzo\Kernel\Configuration;

class Router {

    use ServiceCreation;

    private $routes = array();

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
        $routeConfiguration = new Configuration();
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
