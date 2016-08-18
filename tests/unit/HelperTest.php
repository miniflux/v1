<?php

class HelperTest extends PHPUnit_Framework_TestCase
{
    public function testGenerateToken()
    {
        $token1 = Helper\generate_token();
        $token2 = Helper\generate_token();
        $this->assertNotEquals($token1, $token2);
    }
}
