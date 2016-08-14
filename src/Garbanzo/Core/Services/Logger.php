<?php
namespace Garbanzo\Core\Services;

use Garbanzo\Kernel\Traits\ServiceCreation;

class Logger {

    use ServiceCreation;

    public function crudeLog($message) {
        echo $message . "\n";
    }

}
