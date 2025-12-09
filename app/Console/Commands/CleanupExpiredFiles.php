<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FileManager;

class CleanupExpiredFiles extends Command
{

    // 命令名称 (在终端运行的名字)
    protected $signature = 'files:cleanup';

    // 命令描述 (php artisan list 时看到的说明)
    protected $description = 'Clean up expired files and recycle share codes';


    // 执行逻辑

    public function handle(FileManager $fileManager)
    {
        $this->info('Starting cleanup process...');

        try {
            // 调用清理逻辑 - Call the cleanup logic
            $count = $fileManager->cleanAllExpired();
            
            $this->info("Success! Cleaned up {$count} expired files.");
            
            \Log::info("Scheduled Cleanup: Removed {$count} files.");
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            \Log::error("Cleanup Failed: " . $e->getMessage());
        }
    }
}