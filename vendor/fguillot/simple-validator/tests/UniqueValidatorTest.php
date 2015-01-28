<?php

require_once 'src/SimpleValidator/Base.php';
require_once 'src/SimpleValidator/Validators/Unique.php';

use SimpleValidator\Validators\Unique;

class UniqueValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testValidator()
    {
    	$pdo = new PDO('sqlite::memory:');
    	$pdo->exec('CREATE TABLE mytable (id INTEGER, toto TEXT)');

        $message = 'field must be unique';

        $v = new Unique('toto', $message, $pdo, 'mytable');

        $this->assertEquals($message, $v->getErrorMessage());

        $this->assertTrue($v->execute(array('toto' => '')));
        $this->assertTrue($v->execute(array('toto' => null)));

        $this->assertTrue($v->execute(array('toto' => 'titi')));

        $pdo->exec("INSERT INTO mytable VALUES ('1', 'truc')");

        $this->assertTrue($v->execute(array('toto' => 'titi')));

        $pdo->exec("INSERT INTO mytable VALUES ('2', 'titi')");

        $this->assertFalse($v->execute(array('toto' => 'titi')));
        
        $this->assertTrue($v->execute(array('toto' => 'titi', 'id' => '2')));

        $this->assertFalse($v->execute(array('toto' => 'truc', 'id' => '2')));
    }
    
    public function testValidatorWithArray()
    {
    	$pdo = new PDO('sqlite::memory:');
    	$pdo->exec('CREATE TABLE mytable (id INTEGER, toto TEXT, tata TEXT)');

        $message = 'field must be unique';

        $v = new Unique(array('toto', 'tata'), $message, $pdo, 'mytable');

        $this->assertEquals($message, $v->getErrorMessage());

        $this->assertTrue($v->execute(array('toto' => '')));
        $this->assertTrue($v->execute(array('toto' => null)));

        $this->assertTrue($v->execute(array('toto' => 'bidule')));
        $this->assertTrue($v->execute(array('toto' => 'bidule', 'tata' => 'machin')));
        $this->assertTrue($v->execute(array('toto' => 'bidule', 'toutou' => 'machin')));

        $pdo->exec("INSERT INTO mytable VALUES ('1', 'truc', 'muche')");

        $this->assertTrue($v->execute(array('toto' => 'truc')));
        $this->assertTrue($v->execute(array('toto' => 'bidule')));
        
        $this->assertTrue($v->execute(array('toto' => 'bidule', 'tata' => 'miouch')));
        $this->assertTrue($v->execute(array('toto' => 'bidule', 'tata' => 'muche')));
        $this->assertTrue($v->execute(array('toto' => 'bidule', 'toutou' => 'muche')));

        $this->assertFalse($v->execute(array('toto' => 'truc', 'tata' => 'muche')));
        $this->assertTrue($v->execute(array('toto' => 'truc', 'tata' => 'miouch')));
        
        $pdo->exec("INSERT INTO mytable VALUES ('2', 'bidule', 'machin')");

        $this->assertTrue($v->execute(array('toto' => 'bidule')));
        $this->assertTrue($v->execute(array('toto' => 'truc')));
        
        $this->assertTrue($v->execute(array('toto' => 'bidule', 'tata' => 'muche')));
        $this->assertFalse($v->execute(array('toto' => 'bidule', 'tata' => 'machin')));
        $this->assertTrue($v->execute(array('toto' => 'bidule', 'tata' => 'muche', 'id' => '2')));
        $this->assertTrue($v->execute(array('toto' => 'bidule', 'tata' => 'machin', 'id' => '2')));

        $this->assertTrue($v->execute(array('toto' => 'truc', 'id' => '2')));
        $this->assertFalse($v->execute(array('toto' => 'truc', 'tata' => 'muche', 'id' => '2')));
        $this->assertTrue($v->execute(array('toto' => 'truc', 'tata' => 'miouch', 'id' => '2')));
    }
}