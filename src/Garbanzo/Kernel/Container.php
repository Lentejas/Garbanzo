<?php
namespace Garbanzo\Kernel;

use Garbanzo\Kernel\Interfaces\PluginInterface;
use Garbanzo\Kernel\Interfaces\ContainerInterface;
use Exception;
use InvalidArgumentException;

class Container implements ContainerInterface{

    protected $serviceManager;
    private $loadedPlugins = array();
    private $middlewares = array();
    private $services = array();
    private $loader;

    public function __construct($loader, $serviceManager) {
        $this->setLoader($loader);
        $this->setServiceManager($serviceManager);
    }

    public function setLoader($loader) {
        $this->loader = $loader;
    }

    public function setServiceManager($serviceManager) {
        $this->serviceManager = $serviceManager;
    }

    public function loadPlugin($name) {
        if(! array_key_exists($name, $this->loadedPlugins)) {
            $this->loadedPlugins[$name] = $this->loader->load($name);
            $this->loadedPlugins[$name]->setContainer($this);
            $this->loadedPlugins[$name]->create();
            $this->loadedPlugins[$name]->loadDependencies();
            $this->serviceManager->registerFromPlugin($this->loadedPlugins[$name]);
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
        $service = $this->serviceManager->get($name);

        if ($service === NULL) {
            $nameParts = explode('.', $this->serviceManager->getName($name));
            $plugin = $this->loadedPlugins[$nameParts[0]];
            $service = $this->serviceManager->instatiateService($name, $plugin);
            $service->setContainer($this);
        }
        return $service;
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
    public function registerServices($services) {
        $this->serviceManager->register($services);
    }


}
