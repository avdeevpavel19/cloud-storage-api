<?php

namespace App\DTO\Api;

class FolderDTO
{
    public int    $id;
    public int    $user_id;
    public ?int   $parent_folder_id;
    public string $name;

    public function __construct(int $id, int $user_id, ?int $parent_folder_id, string $name)
    {
        $this->id               = $id;
        $this->user_id          = $user_id;
        $this->parent_folder_id = $parent_folder_id;
        $this->name             = $name;
    }
}
