<?php

use SimpleValidator\Validators\NotEquals;

class EmailValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testValidator()
    {
        $message = 'should not be equal';

        $v = new NotEquals('toto', 'titi', $message);

        $this->assertEquals($message, $v->getErrorMessage());
        
        $this->assertFalse($v->execute(array('toto' => 'test', 'titi' => 'test')));
        $this->assertTrue($v->execute(array('toto' => 'test', 'titi' => 'testest')));
        
        $this->assertTrue($v->execute(array('toto' => 'test')));
        $this->assertTrue($v->execute(array('toto' => 'test', 'titi' => '')));
        $this->assertTrue($v->execute(array('toto' => 'test', 'titi' => null)));
        $this->assertTrue($v->execute(array('toto' => 'test')));
        $this->assertTrue($v->execute(array('toto' => '', 'titi' => 'test')));
        $this->assertTrue($v->execute(array('toto' => null, 'titi' => 'test')));
        $this->assertTrue($v->execute(array('titi' => 'test')));

        $this->assertTrue($v->execute(array()));
        $this->assertTrue($v->execute(array('toto' => '')));
        $this->assertTrue($v->execute(array('toto' => null)));
        $this->assertTrue($v->execute(array('titi' => '')));
        $this->assertTrue($v->execute(array('titi' => null)));
    }
}
