<?php

use Miniflux\Model;

require_once __DIR__.'/BaseTest.php';

class GroupModelTest extends BaseTest
{
    public function testCreateGroup()
    {
        $this->assertEquals(2, Model\User\create_user('somebody', 'test'));

        $this->assertEquals(1, Model\Group\create_group(1, 'tag'));
        $this->assertEquals(1, Model\Group\create_group(1, 'tag'));

        $this->assertEquals(1, Model\Group\get_group_id_from_title(1, 'tag'));
        $this->assertFalse(Model\Group\get_group_id_from_title(1, 'notfound'));

        $this->assertEquals(2, Model\Group\create_group(2, 'tag'));
        $this->assertEquals(2, Model\Group\create_group(2, 'tag'));
    }

    public function testGetAll()
    {
        $this->assertSame(array(), Model\Group\get_all(1));

        $this->assertEquals(1, Model\Group\create_group(1, 'tag 1'));
        $this->assertEquals(2, Model\Group\create_group(1, 'tag 2'));

        $groups = Model\Group\get_all(1);
        $this->assertCount(2, $groups);

        $this->assertEquals(1, $groups[0]['id']);
        $this->assertEquals('tag 1', $groups[0]['title']);

        $this->assertEquals(2, $groups[1]['id']);
        $this->assertEquals('tag 2', $groups[1]['title']);
    }

    public function testAssociation()
    {
        $this->assertCreateFeed($this->buildFeed());
        $this->assertEquals(1, Model\Group\create_group(1, 'tag 1'));
        $this->assertEquals(2, Model\Group\create_group(1, 'tag 2'));
        $this->assertEquals(3, Model\Group\create_group(1, 'tag 3'));

        $this->assertTrue(Model\Group\update_feed_groups(1, 1, array(1, 2), 'tag 4'));

        $this->assertEquals(array(1), Model\Group\get_feed_ids_by_group(1));
        $this->assertEquals(array(1), Model\Group\get_feed_ids_by_group(2));
        $this->assertEquals(array(), Model\Group\get_feed_ids_by_group(3));
        $this->assertEquals(array(1), Model\Group\get_feed_ids_by_group(4));

        $groups = Model\Group\get_feed_groups(1);
        $expected_groups = array(
            array('id' => 1, 'title' => 'tag 1'),
            array('id' => 2, 'title' => 'tag 2'),
            array('id' => 4, 'title' => 'tag 4'),
        );

        $this->assertEquals($expected_groups, $groups);
        $this->assertEquals(array(1, 2, 4), Model\Group\get_feed_group_ids(1));
        $this->assertEquals(array(), Model\Group\get_feed_group_ids(2));

        $expected = array(
            1 => array(1),
            2 => array(1),
            4 => array(1),
        );

        $this->assertEquals($expected, Model\Group\get_groups_feed_ids(1));

        $this->assertTrue(Model\Group\update_feed_groups(1, 1, array(1, 3)));
        $this->assertEquals(array(1, 3), Model\Group\get_feed_group_ids(1));
    }
}
