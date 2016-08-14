<?php
namespace Garbanzo\Core\Services;

use Garbanzo\Kernel\Interfaces\ContainerInterface;

class Security {

    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function isLoggedIn() {

    }
}
