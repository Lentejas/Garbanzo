<?php
namespace Garbanzo\Core\Services;

use Garbanzo\Kernel\Interfaces\ContainerInterface;
use Garbanzo\Kernel\Configuration;

class JsonConfig extends Configuration{

    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
}
