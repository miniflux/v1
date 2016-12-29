<?php

use Miniflux\Model;

require_once __DIR__.'/BaseTest.php';

class UserModelTest extends BaseTest
{
    public function testGetByUsername()
    {
        $user = Model\User\get_user_by_username('admin');
        $this->assertEquals(1, $user['id']);
        $this->assertEquals('admin', $user['username']);
        $this->assertEquals(1, $user['is_admin']);
        $this->assertArrayHasKey('password', $user);
        $this->assertArrayHasKey('last_login', $user);

        $user = Model\User\get_user_by_username('missing');
        $this->assertNull($user);
    }

    public function testGetById()
    {
        $user = Model\User\get_user_by_id(1);
        $this->assertEquals(1, $user['id']);
        $this->assertEquals('admin', $user['username']);
        $this->assertEquals(1, $user['is_admin']);
        $this->assertArrayHasKey('password', $user);
        $this->assertArrayHasKey('last_login', $user);

        $user = Model\User\get_user_by_id(42);
        $this->assertNull($user);
    }

    public function testGetByIdWithoutPassword()
    {
        $user = Model\User\get_user_by_id_without_password(1);
        $this->assertEquals('admin', $user['username']);
        $this->assertArrayNotHasKey('password', $user);

        $user = Model\User\get_user_by_id_without_password(42);
        $this->assertNull($user);
    }

    public function testLoginDate()
    {
        $this->assertTrue(Model\User\set_last_login_date(1));

        $user = Model\User\get_user_by_username('admin');
        $this->assertEquals(time(), $user['last_login'], '', 1);
    }

    public function testCreateUser()
    {
        $this->assertEquals(2, Model\User\create_user('foobar', 'test'));

        $user = Model\User\get_user_by_id(2);
        $this->assertEquals(2, $user['id']);
        $this->assertEquals('foobar', $user['username']);
        $this->assertEquals(0, $user['is_admin']);
        $this->assertNotEquals('test', $user['password']);
    }

    public function testCreateUserWithTrailingSpaces()
    {
        $this->assertEquals(2, Model\User\create_user('foobar ', ' test'));

        $user = Model\User\get_user_by_id(2);
        $this->assertEquals(2, $user['id']);
        $this->assertEquals('foobar', $user['username']);
        $this->assertEquals(0, $user['is_admin']);
        $this->assertTrue(password_verify('test', $user['password']));
    }

    public function testRemoveUser()
    {
        $this->assertEquals(2, Model\User\create_user('foobar', 'test'));
        $this->assertTrue(Model\User\remove_user(2));
        $this->assertNull(Model\User\get_user_by_id(2));
    }

    public function testUpdateUser()
    {
        $this->assertEquals(2, Model\User\create_user('foobar', 'test'));

        $this->assertTrue(Model\User\update_user(2, 'someone'));
        $user1 = Model\User\get_user_by_id(2);
        $this->assertEquals('someone', $user1['username']);

        $this->assertTrue(Model\User\update_user(2, 'someone', 'password', true));
        $user2 = Model\User\get_user_by_id(2);
        $this->assertEquals('someone', $user2['username']);
        $this->assertEquals(1, $user2['is_admin']);
        $this->assertNotEquals($user1['password'], $user2['password']);
    }

    public function testGenerateTokens()
    {
        $user1 = Model\User\get_user_by_id(1);
        $this->assertTrue(Model\User\regenerate_tokens(1));

        $user2 = Model\User\get_user_by_id(1);
        $this->assertNotEquals($user1['api_token'], $user2['api_token']);
        $this->assertNotEquals($user1['bookmarklet_token'], $user2['bookmarklet_token']);
        $this->assertNotEquals($user1['cronjob_token'], $user2['cronjob_token']);
        $this->assertNotEquals($user1['fever_token'], $user2['fever_token']);
        $this->assertNotEquals($user1['feed_token'], $user2['feed_token']);
    }
}
