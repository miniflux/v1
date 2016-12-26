<?php

use Miniflux\Model;

require_once __DIR__.'/BaseTest.php';

class ConfigModelTest extends BaseTest
{
    public function testGetAllAndSave()
    {
        $settings = Model\Config\get_all(1);
        $this->assertNotEmpty($settings);
        $this->assertArrayHasKey('pinboard_enabled', $settings);

        $this->assertTrue(Model\Config\save(1, array('foobar' => 'something')));

        $settings = Model\Config\get_all(1);
        $this->assertEquals('something', $settings['foobar']);

        $this->assertTrue(Model\Config\save(1, array('foobar' => 'something else')));

        $settings = Model\Config\get_all(1);
        $this->assertEquals('something else', $settings['foobar']);
    }
}
