<?php

namespace App\Console\Commands;

use App\Models\File;
use Illuminate\Console\Command;

class DeleteExpiredFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-expired-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        File::where('expires_at', '<', now())->delete();
    }
}
