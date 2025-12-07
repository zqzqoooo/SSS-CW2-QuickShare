<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FileManager; // 引入您的服务

class CleanupExpiredFiles extends Command
{
    /**
     * 1. 命令名称 (在终端运行的名字)
     */
    protected $signature = 'files:cleanup';

    /**
     * 2. 命令描述 (php artisan list 时看到的说明)
     */
    protected $description = 'Clean up expired files and recycle share codes';

    /**
     * 3. 执行逻辑
     */
    public function handle(FileManager $fileManager)
    {
        $this->info('Starting cleanup process...');

        try {
            // 调用我们之前写好的核心逻辑
            $count = $fileManager->cleanAllExpired();
            
            $this->info("Success! Cleaned up {$count} expired files.");
            
            // 可选：写日志，方便排查
            \Log::info("Scheduled Cleanup: Removed {$count} files.");
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            \Log::error("Cleanup Failed: " . $e->getMessage());
        }
    }
}