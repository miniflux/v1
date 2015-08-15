<?php

require_once 'vendor/autoload.php';
require_once __DIR__.'/SchemaFixture.php';

class PostgresSchemaTest extends PHPUnit_Framework_TestCase
{
    private $db;

    public function setUp()
    {
        $this->db = new PicoDb\Database(array('driver' => 'postgres', 'hostname' => 'localhost', 'username' => 'postgres', 'password' => 'postgres', 'database' => 'picodb'));
        $this->db->getConnection()->exec('DROP TABLE IF EXISTS test1');
        $this->db->getConnection()->exec('DROP TABLE IF EXISTS test2');
        $this->db->getConnection()->exec('DROP TABLE IF EXISTS schema_version');
    }

    public function testMigrations()
    {
        $this->assertTrue($this->db->schema()->check(2));
        $this->assertEquals(2, $this->db->getDriver()->getSchemaVersion());
    }

    public function testFailedMigrations()
    {
        $this->assertEquals(0, $this->db->getDriver()->getSchemaVersion());
        $this->assertFalse($this->db->schema()->check(3));
        $this->assertEquals(0, $this->db->getDriver()->getSchemaVersion());

        $logs = $this->db->getLogMessages();
        $this->assertNotEmpty($logs);
        $this->assertStringStartsWith('\Schema\version_3 => SQLSTATE[42601]: Syntax error', $logs[0]);
    }
}
