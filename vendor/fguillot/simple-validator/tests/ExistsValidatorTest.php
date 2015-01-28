<?php

require_once 'src/SimpleValidator/Base.php';
require_once 'src/SimpleValidator/Validators/Unique.php';
require_once 'src/SimpleValidator/Validators/Exists.php';

use SimpleValidator\Validators\Exists;

class ExistsValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testValidator()
    {
    	$pdo = new PDO('sqlite::memory:');
    	$pdo->exec('CREATE TABLE mytable (id INTEGER, toto TEXT)');

        $message = 'toto doesn\'t exist';

        $v = new Exists('id', $message, $pdo, 'mytable');

        $this->assertEquals($message, $v->getErrorMessage());

        $this->assertTrue($v->execute(array('id' => '')));
        $this->assertTrue($v->execute(array('id' => null)));

        $this->assertFalse($v->execute(array('id' => '1')));

        $pdo->exec("INSERT INTO mytable VALUES ('1', 'truc')");

        $this->assertTrue($v->execute(array('id' => '1')));
        $this->assertFalse($v->execute(array('id' => '0')));
        $this->assertTrue($v->execute(array('id' => '')));
        $this->assertTrue($v->execute(array('id' => null)));

        $pdo->exec("INSERT INTO mytable VALUES ('2', 'muche')");

        $this->assertTrue($v->execute(array('id' => '2')));
        $this->assertTrue($v->execute(array('id' => '1')));
        $this->assertFalse($v->execute(array('id' => '0')));
        $this->assertTrue($v->execute(array('id' => '')));
        $this->assertTrue($v->execute(array('id' => null)));
    }
    
    public function testValidatorWithDifferentKey()
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->exec('CREATE TABLE mytable (id INTEGER, toto TEXT)');
    
        $message = 'toto doesn\'t exist';
    
        $v = new Exists('toto_id', $message, $pdo, 'mytable', 'id');
    
        $this->assertEquals($message, $v->getErrorMessage());
    
        $this->assertTrue($v->execute(array('toto_id' => '')));
        $this->assertTrue($v->execute(array('toto_id' => null)));
    
        $this->assertFalse($v->execute(array('toto_id' => '1')));
    
        $pdo->exec("INSERT INTO mytable VALUES ('1', 'truc')");
    
        $this->assertTrue($v->execute(array('toto_id' => '1')));
        $this->assertFalse($v->execute(array('toto_id' => '0')));
        $this->assertTrue($v->execute(array('toto_id' => '')));
        $this->assertTrue($v->execute(array('toto_id' => null)));
    
        $pdo->exec("INSERT INTO mytable VALUES ('2', 'muche')");
    
        $this->assertTrue($v->execute(array('toto_id' => '2')));
        $this->assertTrue($v->execute(array('toto_id' => '1')));
        $this->assertFalse($v->execute(array('toto_id' => '0')));
        $this->assertTrue($v->execute(array('toto_id' => '')));
        $this->assertTrue($v->execute(array('toto_id' => null)));
    }
}