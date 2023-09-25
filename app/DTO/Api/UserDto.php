<?php

namespace App\DTO\Api;

class UserDto
{
    public int    $id;
    public string $login;
    public string $email;
    public int    $diskSpace;

    public function __construct(int $id, string $login, string $email, int $diskSpace)
    {
        $this->id        = $id;
        $this->login     = $login;
        $this->email     = $email;
        $this->diskSpace = $diskSpace;
    }
}
