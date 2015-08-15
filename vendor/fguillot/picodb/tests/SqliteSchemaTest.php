<?php

require_once 'vendor/autoload.php';
require_once __DIR__.'/SchemaFixture.php';

class SqliteSchemaTest extends PHPUnit_Framework_TestCase
{
    private $db;

    public function setUp()
    {
        $this->db = new PicoDb\Database(array('driver' => 'sqlite', 'filename' => ':memory:'));
    }

    public function testMigrations()
    {
        $this->assertTrue($this->db->schema()->check(2));
        $this->assertEquals(2, $this->db->getDriver()->getSchemaVersion());
    }

    public function testFailedMigrations()
    {
        $this->assertFalse($this->db->schema()->check(3));
        $this->assertEquals(0, $this->db->getDriver()->getSchemaVersion());

        $logs = $this->db->getLogMessages();
        $this->assertNotEmpty($logs);
        $this->assertEquals('\Schema\version_3 => SQLSTATE[HY000]: General error: 1 near "TABL": syntax error', $logs[0]);
    }
}
