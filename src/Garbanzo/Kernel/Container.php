<?php
namespace Garbanzo\Kernel;

use Garbanzo\Kernel\Interfaces\PluginInterface;
use Garbanzo\Kernel\Interfaces\ContainerInterface;
use Exception;
use InvalidArgumentException;

class Container implements ContainerInterface{

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

    public function loadPlugin($name) {
        if(! array_key_exists($name, $this->loadedPlugins)) {
            $this->loadedPlugins[$name] = $this->loader->load($name);
            $this->loadedPlugins[$name]->setContainer($this);
            $this->loadedPlugins[$name]->create();
            $this->loadedPlugins[$name]->loadDependencies();
            $services = $this->loadedPlugins[$name]->getDefinedServices();
            $namespace = $this->loadedPlugins[$name]->getServicesNamespace();
            foreach ($services as $nameService => $service) {
                $this->addService($namespace . '.' . $nameService, $service);
            }
        }
        return $this->loadedPlugins[$name];
    }

    public function getMiddleware($id) {
        if(! array_key_exists($id, $this->middlewares)) {
            throw new Exception('The middleware ' . $id . ' is not registered');
        }
        return $this->middlewares[$id];

    }

    public function getMiddlewares() {
        return $this->middlewares;
    }

    public function get($name) {
        return $this->getService($name);
    }

    public function getService($name) {
        if(! array_key_exists($name, $this->services)) {
            throw new Exception('The service ' . $name . ' is not registered');
        }
        if ($this->services[$name]['object'] === NULL) {
            $nameParts = explode('.', $name);
            $plugin = $this->loadedPlugins[$nameParts[0]];
            $this->services[$name]['object'] = new $this->services[$name]['class']();
            $this->services[$name]['object']->setPlugin($plugin);
            $this->services[$name]['object']->setContainer($this);
        } else if (is_callable($this->services[$name]['object'])) {
            return $this->services[$name]['object']();
        }
        return $this->services[$name]['object'];
    }

    public function getServices() {
        return $this->services;
    }

    public function addPlugin(PluginInterface $plugin) {
        throw new Exception('NotImplemented');
        if(array_key_exists($plugin->getName(), $this->loadedPlugins)) {
            throw new Exception('The plugin ' . $plugin->getName() . ' is already registered');
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
        } else if (is_object($service)) {
            $this->services[$name] = array(

                'object' => $service,
                'class' => get_class($service),
            );
            return;
        } else if (! class_exists($service)) {
            throw new InvalidArgumentException('The provided class (' . $service . ') does not exist');
        }
        $this->services[$name] = array(
            'class' => $service,
            'object' => NULL
        );
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
