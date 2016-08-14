<?php
namespace Garbanzo\Kernel\Interfaces;

use Garbanzo\Kernel\Interfaces\ContainerInterface;

interface PluginInterface {
    public function __construct($name, $mainConfigFileName);

    public function setContainer(ContainerInterface $container);

    public function getDefinedServices();

    public function loadDependencies();

    public function create();

    public function getServicesNamespace();

}
