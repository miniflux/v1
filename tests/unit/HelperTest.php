<?php

use Miniflux\Helper;
use Miniflux\Model;
use Miniflux\Session\SessionStorage;

require_once __DIR__.'/BaseTest.php';

class HelperTest extends BaseTest
{
    public function testConfig()
    {
        SessionStorage::getInstance()->setUser(array('id' => 1, 'user_id' => 1, 'username' => 'admin', 'is_admin' => 1));

        $this->assertNull(Helper\config('option'));
        $this->assertSame('default', Helper\config('option', 'default'));

        $this->assertTrue(Model\Config\save(1, array('option1' => '1', 'option2' => '0')));

        $this->assertTrue(Helper\bool_config('option1'));
        $this->assertFalse(Helper\bool_config('option2'));
        $this->assertFalse(Helper\bool_config('option3'));
        $this->assertTrue(Helper\bool_config('option4', true));
    }

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
