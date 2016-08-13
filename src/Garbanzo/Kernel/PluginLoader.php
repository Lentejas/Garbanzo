<?php
namespace Garbanzo\Kernel;

use Garbanzo\Kernel\Configuration;
use InvalidArgumentException;
use Exception;
use StdClass;

class PluginLoader {

    protected $registeredPlugins;

    public function __construct(Configuration $pluginsConfiguration) {
        $this->registeredPlugins = array();
        $this->register($pluginsConfiguration->getProperties());
    }

    public function register($plugins) {
        foreach ($plugins as $name => $confFile) {
            if (array_key_exists($name, $this->registeredPlugins)) {
                throw new Exception('The plug-in ' . $name . ' is already registered');
            }
            $this->registeredPlugins[$name] = $confFile;
        }
    }

    public function load($name) {
        if (! array_key_exists($name, $this->registeredPlugins)) {
            throw new Exception('The plug-in ' . $name . ' is not registered');
        }
        $pluginConfiguration = $this->loadPluginConfiguration($this->registeredPlugins[$name]);
        $plugin = $this->instantiatePlugin($pluginConfiguration);
        unset($this->registeredPlugins[$name]);
        return $plugin;
    }

    protected function loadPluginConfiguration($configFilePath) {
        if (! file_exists(Configuration::ROOT . $configFilePath)) {
            throw new Exception('The file ' . $configFilePath . ' was not found.');
        }
        $pluginConfiguration = new Configuration();
        $pluginConfiguration->loadFile($configFilePath);
        $this->checkConfiguration($pluginConfiguration, $configFilePath);
        return $pluginConfiguration;
    }

    protected function instantiatePlugin(Configuration $pluginConfiguration) {
        $entryPoint = $pluginConfiguration->get('entry_point');
        return new $entryPoint();
    }

    protected function checkConfiguration(Configuration $configuration, $fileName) {
        if ($pluginConfiguration->get('entry_point') === NULL) {
            throw new Exception('No entry point was defined in the config file: ' . $filename);
        }
        return true;
    }

    public function getRegistered() {
        return $this->registeredPlugins;
    }
}
