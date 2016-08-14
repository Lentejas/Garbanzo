<?php
namespace Garbanzo\Core\Services;

use Garbanzo\Kernel\Interfaces\ContainerInterface;

class Logger {

    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function crudeLog($message) {
        echo $message . "\n";
    }
}
