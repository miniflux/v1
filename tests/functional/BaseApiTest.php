<?php

require_once __DIR__.'/../../app/common.php';

abstract class BaseApiTest extends PHPUnit_Framework_TestCase
{
    protected $adminUser = array();

    public function setUp()
    {
        if (DB_DRIVER === 'postgres') {
            $pdo = new PDO('pgsql:host='.DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
            $pdo->exec('CREATE DATABASE '.DB_NAME.' WITH OWNER '.DB_USERNAME);
        }

        $db = Miniflux\Database\get_connection();
        $this->adminUser = $db->table(Miniflux\Model\User\TABLE)->eq('username', 'admin')->findOne();
    }
}
