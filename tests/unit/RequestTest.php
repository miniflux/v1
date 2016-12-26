<?php

use Miniflux\Request;

require_once __DIR__.'/BaseTest.php';

class RequestTest extends BaseTest
{
    public function testGetIpAddress()
    {
        $_SERVER = array('HTTP_X_REAL_IP' => '127.0.0.1');
        $this->assertEquals('127.0.0.1', Request\get_ip_address());

        $_SERVER = array('HTTP_FORWARDED_FOR' => ' 127.0.0.1, 192.168.0.1');
        $this->assertEquals('127.0.0.1', Request\get_ip_address());

        $_SERVER = array();
        $this->assertEquals('Unknown', Request\get_ip_address());
    }

    public function testGetUserAgent()
    {
        $_SERVER = array();
        $this->assertEquals('Unknown', Request\get_user_agent());

        $_SERVER = array('HTTP_USER_AGENT' => 'foobar');
        $this->assertEquals('foobar', Request\get_user_agent());
    }
}
