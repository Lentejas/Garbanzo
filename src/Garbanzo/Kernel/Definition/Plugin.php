<?php
namespace Garbanzo\Kernel\Definition;

use Garbanzo\Kernel\ConfigurationManager;
use Garbanzo\Kernel\Interfaces\ContainerInterface;
use Garbanzo\Kernel\Configuration;
use ReflectionClass;

abstract class Plugin {

    protected $container;
    /** @var Configuration */
    protected $configuration;
    protected $mainConfigFileName;
    protected $namespace;

    public function __construct(Configuration $configuration) {
        $this->namespace = $configuration->get('name');
        $this->mainConfigFileName = $configuration->get('configuration');
        $this->configuration = $configuration->getConfiguration($this->mainConfigFileName, $this->getPluginRoot());
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
        if (strpos($path, 'vendor') !== false) {
            $pathParts = explode('src', $path);

            $pathParts =array_merge(
                explode('vendor' , $pathParts[0]),
                array_slice($pathParts, 1)
            );
            array_shift($pathParts);
            array_pop($pathParts);
            return '/vendor' . implode($pathParts) . '/';
        } elseif (strpos($path, 'src') !== false) {
            $pathParts = explode('src', $path);

                $pathParts =array_merge(
                    explode('vendor' , $pathParts[0]),
                    array_slice($pathParts, 1)
                );
            array_shift($pathParts);
            return '/src' . implode($pathParts) . '/';
        }
        return dirname($childData->getFileName());
    }


    public function getConfiguration() {
        return $this->configuration;
    }

}
