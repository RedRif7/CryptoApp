<?php

namespace User;

class User
{
    private $usersFile;
    private $users;

    public function __construct()
    {
        $this->usersFile = __DIR__ . '/Data/users.json';
        $this->loadUserData();
    }

    private function loadUserData()
    {
        if (file_exists($this->usersFile)) {
            $this->users = json_decode(file_get_contents($this->usersFile), true);
        } else {
            $this->users = [];
        }
    }

    public function login(string $username, string $password): bool
    {
        foreach ($this->users as $user) {
            if ($user['username'] === $username && $user['password'] === md5($password)) {
                $_SESSION['user'] = $username;
                return true;
            }
        }
        return false;
    }

    public function getAllUsers(): array
    {
        return $this->users ?? [];
    }

    public function saveUserData()
    {
        file_put_contents($this->usersFile, json_encode($this->users));
    }
}
