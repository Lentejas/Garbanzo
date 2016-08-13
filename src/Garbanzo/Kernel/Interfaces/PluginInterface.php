<?php
namespace Garbanzo\Kernel\Interfaces;

use Garbanzo\Kernel\Container;

interface PluginInterface {

    public function setContainer(Container $container);

    public function getDefinedServices();

    public function loadDependencies();

}
