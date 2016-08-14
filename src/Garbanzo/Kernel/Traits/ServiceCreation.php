<?php
namespace Garbanzo\Kernel\Traits;

use Garbanzo\Kernel\Interfaces\ContainerInterface;

trait ServiceCreation {
    protected $container;

    public function setContainer(ContainerInterface $container) {
        $this->container = $container;
    }

}
