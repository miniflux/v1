<?php

use Miniflux\Helper;

class HelperTest extends BaseTest
{
    public function testGenerateToken()
    {
        $token1 = Helper\generate_token();
        $token2 = Helper\generate_token();
        $this->assertNotEquals($token1, $token2);
    }

    public function testGenerateCsrf()
    {
        $_SESSION = array();

        $token1 = Helper\generate_csrf();
        $token2 = Helper\generate_csrf();
        $this->assertNotEquals($token1, $token2);
    }

    public function testCheckCsrf()
    {
        $token = Helper\generate_csrf();
        $this->assertTrue(Helper\check_csrf($token));
        $this->assertFalse(Helper\check_csrf('test'));
    }

    public function testCheckCsrfValues()
    {
        $values = array('field' => 'value');
        Helper\check_csrf_values($values);
        $this->assertEmpty($values);

        $values = array('field' => 'value', 'csrf' => Helper\generate_csrf());
        Helper\check_csrf_values($values);
        $this->assertEquals(array('field' => 'value'), $values);
    }
}
