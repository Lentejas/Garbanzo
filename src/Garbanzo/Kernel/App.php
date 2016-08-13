<?php
namespace Garbanzo\Kernel;

use Garbanzo\Kernel\Container;

class App {

    const PROD = 'prod';
    const DEV = 'dev';
    const TEST = 'test';

    private $environment;
    private $container;
    private $configuration;

    public function __construct($environment = self::PROD) {
        $this->environment = $environment;
        $this->container = new Container();
        $this->configuration = new Configuration($this->environment);
    }

    public function run() {
        
    }

    public function getConfiguration() {
        return $this->configuration;
    }
}
