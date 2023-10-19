<?php

namespace App\DTO\Api;

class FolderDTO
{
    public int    $id;
    public int    $user_id;
    public string $name;

    public function __construct(int $id, int $user_id, string $name)
    {
        $this->id      = $id;
        $this->user_id = $user_id;
        $this->name    = $name;
    }
}
