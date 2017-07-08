<?php
namespace Garbanzo\Kernel;

use BadMethodCallException;
use Exception;
use Garbanzo\Kernel\Exception\ConfigurationNotFoundException;
use InvalidArgumentException;

/**
 * This class allows for the management of configuration files
 * @package Garbanzo\Kernel
 */
class ConfigurationManager {

    public static $ROOT = NULL;
    const DEFAULT_CONFIG_DIRECTORY = '/config/';
    private $config_path;
    private $loadedFiles;

    /**
     * @var $serverRootPath string At first call, must be provided. After, only provide in order to overwrite
     */
    public function __construct($serverRootPath = NULL) {
        if (self::$ROOT === NULL && $serverRootPath === NULL) {
            throw new BadMethodCallException('The server root path must be defined at least once.');
        } elseif ($serverRootPath !== NULL && (! $this->isCorrectPath($serverRootPath))) {
            throw new InvalidArgumentException('The path provided is not a correct path.');
        } elseif ($serverRootPath !== NULL) {
            self::$ROOT = $serverRootPath;
        }
        $this->config_path = NULL;
        $this->loadedFiles = array();
    }


    public function setConfigDirectory($path) {
        return new Configuration($this, $path);
    }

    public function getConfigurationData(string $fileName, string $configDirectory = self::DEFAULT_CONFIG_DIRECTORY) {
        $configDirectory = $this->correctPathFormat($configDirectory);
        if (strpos($fileName, '/') !== false) {
            $pos = strpos($fileName, '/');
            $lastPos = $pos;
            while(false !== $pos = strpos($fileName, '/', $pos + 1)){$lastPos = $pos;}
            $configDirectoryPart = substr($fileName, 0, $lastPos + 1);
            $fileName = substr($fileName, $lastPos + 1);
            try {
                return $this->getConfigurationData($fileName, $configDirectoryPart . $configDirectory);
            }catch (ConfigurationNotFoundException $e) {
                return $this->getConfigurationData($fileName, $configDirectory . $configDirectoryPart);
            }
        }
        if (! array_key_exists($configDirectory, $this->loadedFiles)) {
            $this->loadedFiles[$configDirectory] = array();
        }
        if (! array_key_exists($fileName, $this->loadedFiles[$configDirectory]) ) {
            $this->loadFile($fileName, $configDirectory);
        }
        return $this->loadedFiles[$configDirectory][$fileName]->getProperties();
    }

    public function getConfigurationFile($fileName, $configDirectory = self::DEFAULT_CONFIG_DIRECTORY) {
        if (! array_key_exists($configDirectory, $this->loadedFiles)) {
            $this->loadedFiles[$configDirectory] = array();
        }
        if (! array_key_exists($fileName, $this->loadedFiles[$configDirectory]) ) {
            $this->loadFile($fileName, $configDirectory);
        }
        return $this->loadedFiles[$configDirectory][$fileName];
    }

    private function loadFile(string $fileName, string $configDirectory) {
        $file = new Configuration($this, $configDirectory, $fileName);

        $path = $this->generateFilePath($fileName, $configDirectory);
        if (! file_exists($path)) {
            $path = $this->generateFilePath($fileName, self::DEFAULT_CONFIG_DIRECTORY, true);
            if (! file_exists($path)) {
                throw new ConfigurationNotFoundException('File not found :' . $path);
            }
        }
        $file->setProperties($this->getFileData($path));

        if ($file->getProperties() === NULL) {
            throw new Exception('The file ' . $path . ' could not be parsed.');
        }

        if (App::getEnvironment() !== App::PROD) {
            $dataProd = $this->loadDataFromProd($fileName);
            $file->setProperties($this->concatProperties($file->getProperties(), $dataProd));
        }
        $this->loadedFiles[$configDirectory][$fileName] = $file;
    }

    private function correctPathFormat($path) {
        $path = str_replace('//', '/', $path);
        return $path;
    }

    private function getFileData($path) {
        return json_decode(file_get_contents($path));
    }

    private function loadDataFromProd($fileName) {
        $path = $this->generateFilePath($fileName, self::DEFAULT_CONFIG_DIRECTORY, true);
        if (! file_exists($path)) {
            return array();
        }
        return $this->loadData($path);
    }

    private function concatProperties($oldProperties, $newProperties) {
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

    private function generateFilePath(string $fileName, $configDirectory, $default = false) {
        $file = self::$ROOT;
        if (strpos($fileName, $configDirectory) === false) {
            $file.= $configDirectory;
        }
        if ( (! $default) && App::getEnvironment() !== App::PROD) {
            $fileNameParts = explode('.', $fileName);
            $file.= $fileNameParts[0] . '_' . App::getEnvironment() . '.' . $fileNameParts[1];
        } else {
            $file.= $fileName;
        }

        return $file;
    }

    private function isCorrectPath($path) {
        if (! is_string($path)) {
            return false;
        }
        return true;
    }
}
