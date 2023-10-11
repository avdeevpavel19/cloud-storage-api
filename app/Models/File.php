<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'folder_id',
        'file',
        'name',
        'sizeMB',
        'format',
        'path',
        'hash',
        'deleted_at',
    ];
}
