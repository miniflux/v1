<?php

class HelperTest extends PHPUnit_Framework_TestCase
{
    public function testGetIpAddress()
    {
        $_SERVER = array('HTTP_X_REAL_IP' => '127.0.0.1');
        $this->assertEquals('127.0.0.1', Helper\get_ip_address());

        $_SERVER = array('HTTP_FORWARDED_FOR' => ' 127.0.0.1, 192.168.0.1');
        $this->assertEquals('127.0.0.1', Helper\get_ip_address());
    }
}
