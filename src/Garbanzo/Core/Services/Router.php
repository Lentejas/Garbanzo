<?php
namespace Garbanzo\Core\Services;

use Garbanzo\Kernel\Traits\ServiceCreation;
use Garbanzo\Kernel\App;
use Garbanzo\Kernel\Configuration;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Garbanzo\Core\Router\Route;

class Router {

    use ServiceCreation;

    private $routes = array();

    public function addRoutes($routes) {
        foreach ($routes as $route) {
            $this->addRoute($route);
        }
    }

    public function addRoute($routeConfig) {
        $prefix = (empty($routeConfig->prefix)) ? false : $routeConfig->prefix;
        $routeConfig = $this->loadRouteConfiguration($routeConfig->file)->getProperties();
        //print_r($routeConfig);
        foreach ($routeConfig as $route => $config) {
            $this->computeRoute($route, $config, $prefix);
        }
    }

    protected function loadRouteConfiguration($configFilePath) {
        $routeConfiguration = $this->container->get('config')->getConfigurationFile($configFilePath);

        $this->validateRouteFile($routeConfiguration);
        return $routeConfiguration;
    }

    protected function validateRouteFile(Configuration $config) {
        if (property_exists($config,"pattern")){
            throw new Exception("Error route file, must specify pattern");
        }
        if (property_exists($config,"controller_method")){
            throw new Exception("Error route file, must specify controller_method");
        }
        if (property_exists($config,"controller")){
            throw new Exception("Error route file, must specify controller");
        }
    }

    protected function computeRoute($route, $config, $prefix) {
        if (property_exists($config,"http_method")) {
            $method = strtoupper($config->http_method);
        } else {
            $method = "GET";
        }
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
        $methods = explode("|",$method);
        foreach ($methods as $method){
            $this->routes[$method][$route] = $r;
        }
    }

    protected function getFormattedParameter($parameterName, $isLastParameter, $requirements = null, $default = null){
        if ($requirements === null && $default === null
            || !(property_exists($default, $parameterName) && property_exists($requirements, $parameterName))) {
                return "(?P<" . $parameterName . ">.*)/" . ($isLastParameter ? '?' : '');
            } elseif (property_exists($requirements, $parameterName) && ($default === null || !(property_exists($default, $parameterName)))) {
                return "(?P<" . $parameterName . ">".$requirements->$parameterName.")/" . $isLastParameter ? '?' : '';
            } elseif (property_exists($default, $parameterName) && ($requirements === null || !(property_exists($requirements, $parameterName)))) {
                return "(?P<" . $parameterName . ">.*)?/?";
            } else {
                return "(?P<" . $parameterName . ">".$requirements->$parameterName.")?/?";
            }
    }

    protected function getParameters($parameters, $default = null){
        $params = array();
        foreach ($parameters as $param) {
            if ($default === null || (!property_exists($default, $param))) {
                $params[$param] = true;
            }else{
                $params[$param] = false;
            }
        }
        return $params;
    }

    /**
     * @return Route
     */
    public function route(ServerRequestInterface $request) {
        $path = $request->getUri()->getPath();
        $routesToBeMatched = $this->routes[$request->getMethod()];
        $matched = null;
        foreach ($routesToBeMatched as $route) {
            if (preg_match('@' . $route['regex'] . '@', $path, $parameters)) {
                $matched = new Route($route, $parameters);
                break;
            }
        }
        if ($matched === null) {
            throw new Exception(sprintf('No route matching %s', $path));
        }
        return $matched;
    }
}
