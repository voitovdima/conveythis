<?php

namespace App\Console\Commands;

use App\Services\FileService;
use App\Models\File;
use Illuminate\Console\Command;

class DeleteExpiredFiles extends Command
{
    protected $signature = 'files:delete-expired';
    protected $description = 'Delete files that have expired after 24 hours';

    protected $fileService;

    public function __construct(FileService $fileService)
    {
        parent::__construct();
        $this->fileService = $fileService;
    }

    public function handle()
    {
        $expiredFiles = File::where('expires_at', '<=', now())->get();

        foreach ($expiredFiles as $file) {
            $this->fileService->delete($file->id);
            $this->info("Deleted expired file: {$file->name}");
        }

        if ($expiredFiles->isEmpty()) {
            $this->info('No expired files found.');
        }
    }
}
