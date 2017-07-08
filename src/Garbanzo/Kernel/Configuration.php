<?php
namespace Garbanzo\Kernel;

use BadMethodCallException;
use InvalidArgumentException;
use Exception;
use StdClass;

class Configuration {

    private $properties;
    /** @var ConfigurationManager  */
    protected $manager;
    protected $basePath;
    protected $fileName;

    public function __construct(ConfigurationManager $manager, $basePath, $filename = null) {
        $this->manager = $manager;
        $this->basePath = $basePath;
        $this->fileName = $filename;
    }

    public function getConfiguration(string $fileName, string $basePath = ConfigurationManager::DEFAULT_CONFIG_DIRECTORY) {
        if ($this->fileName  === null || ($this->fileName != $fileName)
            || ($this->basePath != $basePath && $basePath !== null)) {

            $this->properties = $this->manager->getConfigurationData($fileName, $basePath);
            $this->fileName = $fileName;
        } else {
            return $this->manager->getConfigurationFile($fileName, $basePath);
        }
        return $this;
    }

    public function getProperties() {
        return $this->properties;
    }

    public function setProperties($properties) {
        $this->properties = $properties;
    }

    public function get($propertyName) {
        $propertyNames = explode('.', $propertyName);
        $property = $this->properties;
        foreach ($propertyNames as $namePart) {
            if (! property_exists($property, $namePart)) {
                return NULL;
            }
            $property = $property->$namePart;
        }
        return $property;
    }
}
