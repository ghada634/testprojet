<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../User.php';

class UserTest extends TestCase
{
    public function testAddUser()
    {
        $user = new User();
        $user->addUser("testUser", "password123");
        $this->assertTrue($user->userExists("testUser"));
    }

    public function testUserExists()
    {
        $user = new User();
        $user->addUser("existingUser", "12345");
        $this->assertTrue($user->userExists("existingUser"));
        $this->assertFalse($user->userExists("nonExistingUser"));
    }

    public function testAuthenticateUser()
    {
        $user = new User();
        $user->addUser("authUser", "secret");
        $this->assertTrue($user->authenticate("authUser", "secret"));
        $this->assertFalse($user->authenticate("authUser", "wrongpass"));
    }
}
