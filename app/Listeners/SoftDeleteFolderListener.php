<?php

namespace App\Listeners;

use App\Models\File;
use App\Models\Folder;

class SoftDeleteFolderListener
{
    /**
     * Create the event listener.
     */
    protected $folder;

    public function __construct(Folder $folder)
    {
        $this->folder = $folder;
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        File::where('folder_id', $event->folder->id)->update(['deleted_at' => now()]);
    }
}
