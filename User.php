<?php

class User
{
    public $users = [];

    public function addUser($username, $password)
    {
        $this->users[$username] = $password;
    }

    public function userExists($username)
    {
        return isset($this->users[$username]);
    }

    public function authenticate($username, $password)
    {
        return isset($this->users[$username]) && $this->users[$username] === $password;
    }
}
