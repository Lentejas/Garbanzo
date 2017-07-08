<?php
namespace Garbanzo\Core\Services;

use Garbanzo\Kernel\Traits\ServiceCreation;
use Garbanzo\Kernel\Definition\Controller;
use Garbanzo\Core\Router\Route;
use Exception;

class ControllerManager {

    use ServiceCreation;

    public function call(Route $route) {
        $controllerClass = $route->getControllerClass();
        if (! class_exists($controllerClass)) {
            throw new Exception('The controller: ' . $controllerClass . ' was not found');
        }
        $controller = new $controllerClass($this->container);
        if (! $controller instanceof Controller) {
            throw new Exception('The class: ' . $controllerClass . ' is not a controller');
        }
        if (! method_exists($controller,  $route->getFunction())) {
            throw new Exception('The controller: ' . $controllerClass . ' does not contain a method called: ' . $route->getFunction());;
        }
        return call_user_func_array(array($controller, $route->getFunction()), $route->getParams());
    }
}
