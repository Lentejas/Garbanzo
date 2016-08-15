<?php
namespace Garbanzo\Kernel;

use BadMethodCallException;
use InvalidArgumentException;
use Exception;
use StdClass;

class Configuration {

    private $properties;
    protected $manager;
    protected $basePath;
    protected $fileName;

    public function __construct($manager, $basePath, $filename = null) {
        $this->manager = $manager;
        $this->basePath = $basePath;
        $this->filename = $filename;
    }

    public function getConfiguration($fileName, $basePath =null) {
        if ($this->filename  === null || ($this->filename != $filename 
            || ($this->basePath != $basePath && $basePath !== null))) {
            $this->properties = $this->manager->getConfigurationData($basePath, $fileName);
            $this->filename = $fileName;
        } else {
            return $this->manager->getConfigurationFile($basePath, $fileName);
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
