<?php
namespace Garbanzo\Kernel;

use Exception;
use StdClass;

class Configuration {
    public static $ROOT = NULL;
    const CONFIG_DIRECTORY = '/config/';

    private $config_path;
    private $properties;
    private $environment;

    public function __construct($environment, $serverRootPath = NULL) {
        if (self::$ROOT === NULL && $serverRootPath === NULL) {
            throw new BadMethodCallException('The server root path must be defined at least once.');
        } elseif ($serverRootPath !== NULL) {
            self::$ROOT = $serverRootPath;
        }
        $this->config_path = NULL;
        $this->environment = $environment;
    }

    public function setConfigRootDirectory($path) {
        $this->config_path = $path;
    }

    public function loadFile($fileName) {
        $path = $this->generateFilePath($fileName);
        if (! file_exists($path)) {
            $path = $this->generateFilePath($fileName, true);
            if (! file_exists($path)) {
                throw new Exception('File not found :' . $path);
            }
        }
        $this->properties = $this->loadData($path);
        if ($this->properties === NULL) {
            throw new Exception('The file ' . $path . ' could not be parsed.');
        }
        if ($this->environment !== App::PROD) {
            $this->addDataFromProd($fileName);
        }
    }

    protected function loadData($path) {
        return json_decode(file_get_contents($path));
    }

    protected function addDataFromProd($fileName) {
        $path = $this->generateFilePath($fileName, true);
        if (! file_exists($path)) {
            throw new Exception('File not found: config file ' . $path . '.json for production.');
        }
        $newProperties = $this->loadData($path);
        $this->properties = $this->getDataToConcat($this->properties, $newProperties);
    }

    protected function getDataToConcat($oldProperties, $newProperties) {
        $property = $oldProperties;
        foreach (get_object_vars($newProperties) as $name => $newProperty) {
            if (! property_exists($oldProperties, $name)) {
                $property->$name = $newProperty;
            } elseif (is_object($newProperty)) {
                $property->$name = $this->getDataToConcat($oldProperties->$name, $newProperty);
            }
        }
        return $property;
    }

    protected function generateFilePath($fileName, $default = false) {
        $file = self::$ROOT;
        if ($this->config_path !== NULL) {
            if ($this->config_path !== '/') {
                $file.= $this->config_path;
            }
        }else {
            $file.= self::CONFIG_DIRECTORY;
        }
        if ( (! $default) && $this->environment !== App::PROD) {
            $fileNameParts = explode('.', $fileName);
            $file.= $fileNameParts[0] . '_' . $this->environment . '.' . $fileNameParts[1];
        } else {
            $file.= $fileName;
        }
        return $file;
    }

    public function getProperties() {
        return $this->properties;
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
