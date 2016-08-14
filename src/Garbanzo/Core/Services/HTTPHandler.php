<?php
namespace Garbanzo\Core\Services;

use Garbanzo\Kernel\Traits\ServiceCreation;
use Garbanzo\Core\HTTP\Uri;
use Garbanzo\Core\HTTP\Request;

class HTTPHandler {

    use ServiceCreation;

    protected $request = NULL;
    protected $response = NULL;

    public function getRequest() {
        if ($this->request === null) {
            $uri = Uri::createFromString($this->generateUri($_SERVER));
            $this->request = new Request($uri);
        }
        return $this->request;
    }

    public function getResponse() {

    }

    public function setResponse() {

    }

    protected function generateUri( $s, $use_forwarded_host = false )
    {
        $ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
        $sp       = strtolower( $s['SERVER_PROTOCOL'] );
        $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
        $port     = $s['SERVER_PORT'];
        $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
        $host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
        $host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
        return $protocol . '://' . $host . $s['REQUEST_URI'];
    }

}
