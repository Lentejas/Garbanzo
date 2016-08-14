<?php
namespace Garbanzo\Kernel\Traits;

use Garbanzo\Kernel\Interfaces\ContainerInterface;

trait ServiceCreation {
    protected $container;
    protected $plugin;

    public function setContainer(ContainerInterface $container) {
        $this->container = $container;
    }

    public function setPlugin($plugin) {
        $this->plugin = $plugin;
    }

}
