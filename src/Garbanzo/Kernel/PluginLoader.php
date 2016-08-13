<?php
namespace Garbanzo\Kernel;

use InvalidArgumentException;

class PluginLoader {

    protected $loadedPlugins;
    protected $registeredPlugins;

    public function __construct($plugins) {
        if (! is_array($plugins)) {
            throw new InvalidArgumentException;
        }
        $this->registeredPlugins = $plugins;
    }

    public function add(PluginInterface $plugin) {
        $this->registeredPlugins[$plugin->getName()] = $plugin;
    }

    public function load($name)

}
