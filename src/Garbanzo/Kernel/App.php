<?php
namespace Garbanzo;

use Slim\App as BaseApp;
use \Slim\Container;

class App {

    private static $app;

    private $settings;
    private $routes;
    private $depencies;
    private $middleware;

    public function __construct($settings = array()) {
        $this->settings = $settings;
    }

    public static function create($settings = array()) {
        if (self::$app == null) {
            self::$app = new static($settings);
        }
        return self::$app;
    }

    public function addRoutes($routes) {
        $this->routes = $routes;

    }

    public function addDepencies($dependencies) {
        $this->dependencies = $dependencies;
    }

    public function addMiddleware($middleware) {
        $this->middleware = $middleware;
    }

    public function run() {
        $container= new Container($this->settings);


        foreach ($this->dependencies as $dependencyName => $dependencyValue) {
            $container[$dependencyName] = $dependencyValue;
        }

        $app = new BaseApp($container);

        foreach ($this->routes as $route) {
            $app->{$route[0]}($route[1], $route[2]);
        }

        $app->run();
    }

}
