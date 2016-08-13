<?php
namespace Garbanzo\Kernel\Interfaces;

interface PluginInterface {
    public function getName();

    public function execute();

    public function addDependency($dependency);
}
