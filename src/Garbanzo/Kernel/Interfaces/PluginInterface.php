<?php
namespace Garbanzo\Kernel\Interfaces;

use Garbanzo\Kernel\Container;

interface PluginInterface {
    public function __construct($name, $mainConfigFileName);

    public function setContainer(Container $container);

    public function getDefinedServices();

    public function loadDependencies();

    public function create();

    public function getServicesNamespace();

}
