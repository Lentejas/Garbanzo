<?php
namespace Garbanzo\Core\Router;

use Exception;

class Route {

    protected $pattern;

    protected $controllerClass;

    protected $function;

    protected $params;

    public function __construct($array, $parameters = array()) {
        $this->pattern = $array['regex'];
        $this->controllerClass = $array['class'];
        $this->function = $array['function'];
        $this->params = array();
        foreach ($array['params'] as $parameterName => $parameterValue) {
            if (array_key_exists($parameterName, $parameters)) {
                $this->params[$parameterName] = $parameters[$parameterName];
            } else if ($parameterValue) {
                throw new Exception('The parameter: ' . $parameterName . ' is required.');
            }
        }
    }

    public function getPattern() {
        return $this->pattern;
    }

    public function getControllerClass() {
        return $this->controllerClass;
    }

    public function getFunction() {
        return $this->function;
    }

    public function getParams() {
        return $this->params;
    }
}
