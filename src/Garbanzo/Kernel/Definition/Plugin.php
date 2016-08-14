<?php
namespace Garbanzo\Kernel\Definition;

use Garbanzo\Kernel\Interfaces\ContainerInterface;
use Garbanzo\Kernel\Configuration;
use ReflectionClass;

abstract class Plugin {

    protected $container;
    protected $configuration;
    protected $mainConfigFileName;
    protected $namespace;

    public function __construct($name, $mainConfigFileName) {
        $this->mainConfigFileName = $mainConfigFileName;
        $this->configuration = new Configuration();
        $this->configuration->setConfigRootDirectory($this->getPluginRoot());
        //$this->configuration->loadFile($mainConfigFileName);
        $this->namespace = $name;
    }

    public function setContainer(ContainerInterface $container) {
        $this->container = $container;
    }

    abstract public function getDefinedServices();

    abstract public function loadDependencies();

    abstract public function create();

    public function getServicesNamespace() {
        return $this->namespace;
    }

    public function getPluginRoot() {
        $childData = new ReflectionClass($this);
        $path = dirname($childData->getFileName());
        if (strpos($path, 'src')) {
            $pathParts = explode('src', $path);
            array_pop($pathParts);
            return implode($pathParts);
        }
        return dirname($childData->getFileName());
    }

    public function getConfiguration() {
        return $this->configuration;
    }

}
