<?php
namespace Garbanzo\Kernel;

use Garbanzo\Kernel\Interfaces\PluginInterface;
use Exception;
use InvalidArgumentException;

class Container {

    private $loadedPlugins = array();
    private $middlewares = array();
    private $services = array();
    private $loader;

    public function __construct($loader) {
        $this->setLoader($loader);
    }

    public function setLoader($loader) {
        $this->loader = $loader;
    }

    public function getPlugin($name) {
        if(! array_key_exists($name, $this->loadedPlugins)) {
            $this->loadedPlugins[$name] = $this->loader->load($name);
        }
        return $this->loadedPlugins[$name];
    }

    public function getMiddleware($id) {
        if(! array_key_exists($id, $this->middlewares)) {
            throw new Exception('The middleware ' . $id . 'is not registered');
        }
        return $this->middlewares[$id];

    }

    public function getMiddlewares() {
        return $this->middlewares;
    }

    public function getService($name) {
        if(array_key_exists($name, $this->services)) {
            throw new Exception('The service ' . $name . 'is not registered');
        }
        return $this->services[$name];
    }

    public function getServices() {
        return $this->services;
    }

    public function addPlugin(PluginInterface $plugin) {
        throw new Exception('NotImplemented');
        if(array_key_exists($plugin->getName(), $this->loadedPlugins)) {
            throw new Exception('The plugin ' . $plugin->getName() . 'is already registered');
        }
        $this->loadedPlugins[$plugin->getName()] = $plugin;
    }

    public function setPlugins($plugins) {
        throw new Exception('NotImplemented');
        if(! is_array($plugins)) {
            throw new InvalidArgumentException;
        }
        $this->loadedPlugins = array();
        foreach ($plugins as $plugin) {
            $this->addPlugin($plugin);
        }
    }

    public function addMiddleware($callback) {
        if (! is_callable($callback)) {
            throw new InvalidArgumentException;
        }
        $this->middlewares[] = $callback;
    }

    public function setMiddlewares($middlewares) {
        if(! is_array($middlewares)) {
            throw new InvalidArgumentException;
        }
        $this->middlewares = array();
        foreach ($middlewares as $middleware) {
            $this->addMiddleware($middleware);
        }
    }

    public function addService($name, $service) {
        if (! is_string($name)) {
            throw new InvalidArgumentException;
        } else if (array_key_exists($name, $this->services)) {
            throw new Exception('The service ' . $name . 'is already registered');
        } else if (! is_callable($service)) {
            throw new InvalidArgumentException;
        }
        $this->services[$name] = $service;
    }

    public function setServices($services) {
        if(! is_array($services)) {
            throw new InvalidArgumentException;
        }
        $this->services = array();
        foreach ($services as $name => $service) {
            $this->addService($name, $service);
        }
    }


}
