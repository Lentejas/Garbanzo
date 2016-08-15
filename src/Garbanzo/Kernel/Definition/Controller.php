<?php
namespace Garbanzo\Kernel\Definition;

use Garbanzo\Kernel\Interfaces\ContainerInterface;

class Controller {

    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function render($view, $parameter = array()) {
        if ($this->container->exists('renderer')) {
            return $this->container->exists('renderer')->render($view, $parameter);
        }
        throw new Exception('No renderer service available');
    }

}
