<?php
namespace Garbanzo\Core;

use Garbanzo\Kernel\Definition\Plugin;
use Garbanzo\Kernel\Interfaces\ContainerInterface;
use Garbanzo\Kernel\Configuration;
use Garbanzo\Kernel\App;
use Garbanzo\Core\Services\Logger;
use Garbanzo\Core\Services\ControllerManager;
use Garbanzo\Core\Services\Router;
use Garbanzo\Core\Services\HTTPHandler;
use Garbanzo\Core\Services\Security;
use Garbanzo\Core\Services\JsonConfig;

class CorePlugin extends Plugin {

    protected $container;
    protected $configuration;
    protected $mainConfigFileName;
    protected $namespace;


    public function setContainer(ContainerInterface $container) {
        $this->container = $container;
    }

    public function create() {
        //$this->configuration = new JsonConfig();
        //$this->configuration->setContainer($this->container);
        //$this->configuration->setConfigRootDirectory('/src/Garbanzo/Core');
        //$this->configuration->loadFile($this->mainConfigFileName);
    }

    public function getDefinedServices() {
        return array(
            'logger' => Logger::class,
            'http' => HTTPHandler::class,
            'router' => Router::class,
            'security' => Security::class,
            'controller' => ControllerManager::class,
        );
    }

    public function loadDependencies() {
    }

    public function getConfiguration() {
        return $this->configuration;
    }

    public function getServicesNamespace() {
        return $this->namespace;
    }
}
