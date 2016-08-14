<?php
namespace Garbanzo\Core\Services;

use Garbanzo\Kernel\Traits\ServiceCreation;
use Garbanzo\Kernel\App;
use Garbanzo\Kernel\Configuration;

class Router {

    use ServiceCreation;

    private $routes = array();

    public function addRoute($routeConfig) {
        $prefix = (empty($routeConfig->prefix)) ? false : $routeConfig->prefix;
        $routeConfig = $this->loadRouteConfiguration($routeConfig->file)->getProperties();
        //print_r($routeConfig);
        foreach ($routeConfig as $route => $config) {
            $this->computeRoute($route,$config,$prefix);
        }
    }

    protected function loadRouteConfiguration($configFilePath) {
        $routeConfiguration = new Configuration();
        $routeConfiguration->setConfigRootDirectory('/');
        $routeConfiguration->loadFile($configFilePath);
        return $routeConfiguration;
    }

    protected function computeRoute($route, $config, $prefix) {
        $default = (property_exists($config,'default')) ? $config->default : null;
        $requirements = (property_exists($config,'requirements')) ? $config->requirements : null;
        $r = array();
        $r['class'] = $config->controller;
        $r['function'] = $config->controller_method;
        $r['regex'] = ($prefix) ? "/" . $prefix : "";
        preg_match_all("#:([a-z]+)#",$config->pattern,$params);
        $r['params'] = (! empty($params[1])) ?
                $this->getParameters($params[1],$default)
                : null;
        foreach ($params[1] as $param) {
            $config->pattern = preg_replace('#:'.$param.'/?#', $this->getFormattedParameter($param, $param === $params[1][count($params[1]) - 1],$requirements, $default), $config->pattern);
        }
        $r['regex'] .= $config->pattern;
        $this->routes[$route] = $r;
    }

    protected function getFormattedParameter($parameterName, $isLastParameter, $requirements = null, $default = null){
        if ($requirements === null && $default === null
            || !(property_exists($default, $parameterName) && property_exists($requirements, $parameterName))) {
                return "(.*)/" . ($isLastParameter ? '?' : '');
            } elseif (property_exists($requirements, $parameterName) && ($default === null || !(property_exists($default, $parameterName)))) {
                return "(".$requirements->$parameterName.")/" . $isLastParameter ? '?' : '';
            } elseif (property_exists($default, $parameterName) && ($requirements === null || !(property_exists($requirements, $parameterName)))) {
                return "(.*)?/?";
            } else {
                return "(".$requirements->$parameterName.")?/?";
            }
    }

    protected function getParameters($parameters, $default = null){
        $params = array();
        foreach ($parameters as $param) {
            if ($default === null || (!property_exists($default,$param))) {
                $params[$param] = true;
            }else{
                $params[$param] = false;
            }
        }
        return $params;
    }

    public function route($url) {

    }
}
