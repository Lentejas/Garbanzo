<?php
namespace Garbanzo\Kernel;

use Garbanzo\Kernel\Definition\Plugin;
use InvalidArgumentException;
use Exception;

class ServiceManager {

    protected $services;

    protected $alias;

    public function __construct(ConfigurationManager $configurationManager) {
        try {
            $aliasList = $configurationManager->getConfigurationFile('alias.json')->getProperties();
            $this->alias = json_decode(json_encode($aliasList), true);
        }catch (Exception $e) {
            $this->alias = array();
        }
        $this->services = array(
            'garbanzo.config' => array(
                'class' => ConfigurationManager::class,
                'object' => $configurationManager,
            ),
        );
    }

    public function get($name) {
        $name = $this->getName($name);
        if(! $this->exists($name)) {
            throw new Exception('The service ' . $name . ' is not registered');
        }
        if (is_callable($this->services[$name]['object'])) {
            return $this->services[$name]['object']();
        }
        return $this->services[$name]['object'];
    }

    public function getName($name) {
        if( array_key_exists($name, $this->alias)) {
            return $this->alias[$name];
        }
        return $name;
    }

    public function exists($name) {
        $name = $this->getName($name);
        return array_key_exists($name, $this->services);
    }

    public function instatiateService($name, $plugin) {
        $name = $this->getName($name);
        if (isset($this->services[$name]['object'])) {
            return $this->services[$name]['object'];
        }
        $this->services[$name]['object'] = new $this->services[$name]['class']();
        $this->services[$name]['object']->setPlugin($plugin);
        return $this->services[$name]['object'];
    }


    public function getServices() {
        return $this->services;
    }

    public function registerFromPlugin(Plugin $plugin) {
        $services = $plugin->getDefinedServices();
        $namespace = $plugin->getServicesNamespace();
        $this->register($services, $namespace);
    }

    public function register($services, $namespace) {
        if(! is_array($services)) {
            throw new InvalidArgumentException;
        }
        foreach ($services as $name => $service) {
            $this->registerOne($name, $service, $namespace);
        }
    }

    public function registerOne($serviceName, $service, $namespace = '') {
        if (is_object($service)) {
            $this->services[$namespace . '.' . $serviceName] = array(

                'object' => $service,
                'class' => get_class($service),
            );
            return;
        }  elseif (! is_string($serviceName)) {
            throw new InvalidArgumentException;
        } else if (array_key_exists($serviceName, $this->services)) {
            throw new Exception('The service ' . $serviceName . 'is already registered');
        } elseif (! class_exists($service)) {
            throw new InvalidArgumentException('The provided class (' . $service . ') does not exist');
        }
        $this->services[$namespace . '.' . $serviceName] = array(
            'class' => $service,
            'object' => NULL
        );
    }
}
