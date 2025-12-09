<?php

namespace App\Services;

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\FileLog;

class FileManager
{
    protected $codeManager;

    public function __construct(CodeManager $codeManager)
    {
        $this->codeManager = $codeManager;
    }

    /**
     * API: Upload file (上传文件)
     */
    public function uploadFile(UploadedFile $uploadedFile, ?bool $isOneTime = false): File
    {
        // 1. Policy determination (Guest vs. User) (策略判断 (访客 vs 用户))
        $user = Auth::user();
        $days = $user ? config('quickshare.expiration_days.user') : config('quickshare.expiration_days.guest');
        
        // 2. Physical storage (物理存储)
        $path = $uploadedFile->store('uploads');

        // 3. Get share code (获取取件码)
        $code = $this->codeManager->getNextAvailableCode();

        if (!$code) {
            Storage::delete($path); // Rollback (回滚)
            throw new \Exception('System Busy: Share codes exhausted (系统繁忙：取件码耗尽)');
        }

        // 4. Database entry (入库)
        return File::create([
            'user_id' => $user ? $user->id : null,
            'share_code' => $code,
            'original_name' => $uploadedFile->getClientOriginalName(),
            'storage_path' => $path,
            'file_size' => $uploadedFile->getSize(),
            'is_one_time' => $user ? $isOneTime : true,
            'expires_at' => now()->addDays($days),
        ]);
    }

    /**
     * API: Find file (includes expiration check) (查找文件 (含过期检查))
     */
    public function findFileByCode(string $code)
    {
        $file = File::where('share_code', strtoupper($code))->first();

        if (!$file) {
            return ['status' => 404, 'error' => 'Invalid share code (取件码无效)'];
        }

        // Check for expiration (检查过期)
        if (now()->greaterThan($file->expires_at)) {
            $this->deleteFile($file, 'expired'); // Trigger cleanup (触发清理)
            return ['status' => 410, 'error' => 'File expired (文件已过期)'];
        }

        return ['status' => 200, 'file' => $file];
    }

    /**
     * Core business: Handle post-download logic (counting + one-time view) (核心业务：处理下载后的逻辑 (计数 + 阅后即焚))
     */
    public function handlePostDownload(File $file)
    {
        $file->increment('download_count');
        
        // Return whether self-destruct is needed (返回是否需要阅后即焚)
        return $file->is_one_time;
    }

    /**
     * Performs "soft delete": retains database record, but clears physical file and share code
     * (执行“软删除”：保留数据库记录，但清除物理文件和取件码)
     * @param File $file
     * @param string $reason Deletion reason (删除原因)
     */
    public function deleteFile(File $file, string $reason = 'manual')
    {
        // Delete the physical file on the disk (删除硬盘上的物理文件)
        if (Storage::exists($file->storage_path)) {
            Storage::delete($file->storage_path);
        }

        // Return the share code (put it back into the pool for reuse) (归还取件码 (让它回到池子给别人用))
        $this->codeManager->recycleCode($file->share_code);

        // Mark the database record as "deleted" and record the reason (标记数据库记录为“已删除”并记录原因)
        $file->delete_reason = $reason;
        $file->save(); // Save the reason first (先保存原因)
        
        $file->delete(); // Then execute soft delete (sets the deleted_at timestamp) (再执行软删除 (设置 deleted_at 时间戳))
    }

    /**
     * Batch clean up all expired files
     * (批量清理所有过期文件)
     * @return int Number of files cleaned (清理的文件数量)
     */
    public function cleanAllExpired(): int
    {
        // Find all files where expires_at is less than the current time (找出所有 expires_at 小于当前时间的文件)
        $expiredFiles = File::where('expires_at', '<', now())->get();
        
        $count = 0;
        foreach ($expiredFiles as $file) {
            $this->deleteFile($file, 'expired'); // Pass reason: expired (传入原因：过期)
            $count++;
        }
        
        return $count;
    }
}