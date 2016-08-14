<?php
namespace Garbanzo\Core;

use Garbanzo\Kernel\Interfaces\PluginInterface;
use Garbanzo\Kernel\Interfaces\ContainerInterface;
use Garbanzo\Kernel\Configuration;
use Garbanzo\Kernel\App;
use Garbanzo\Core\Services\Logger;
use Garbanzo\Core\Services\Router;
use Garbanzo\Core\Services\HTTPHandler;
use Garbanzo\Core\Services\Security;
use Garbanzo\Core\Services\JsonConfig;

class CorePlugin implements PluginInterface {

    protected $container;
    protected $configuration;
    protected $mainConfigFileName;
    protected $namespace;

    public function __construct($name, $mainConfigFileName) {
        $this->mainConfigFileName = $mainConfigFileName;
        $this->namespace = $name;
    }

    public function setContainer(ContainerInterface $container) {
        $this->container = $container;
    }

    public function create() {
        $this->configuration = new JsonConfig($this->container);
        $this->configuration->setConfigRootDirectory('/src/Garbanzo/Core');
        $this->configuration->loadFile($this->mainConfigFileName);
    }

    public function getDefinedServices() {
        return array(
            'logger' => Logger::class,
            'http' => HTTPHandler::class,
            'config.json' => $this->configuration,
            'router' => Router::class,
            'security' => Security::class,
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
