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
     * API: 上传文件
     */
    public function uploadFile(UploadedFile $uploadedFile, ?bool $isOneTime = false): File
    {
        // 1. 策略判断 (访客 vs 用户)
        $user = Auth::user();
        $days = $user ? config('quickshare.expiration_days.user') : config('quickshare.expiration_days.guest');
        
        // 2. 物理存储
        $path = $uploadedFile->store('uploads');

        // 3. 获取取件码
        $code = $this->codeManager->getNextAvailableCode();

        if (!$code) {
            Storage::delete($path); // 回滚
            throw new \Exception('系统繁忙：取件码耗尽');
        }

        // 4. 入库
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
     * API: 查找文件 (含过期检查)
     */
    public function findFileByCode(string $code)
    {
        $file = File::where('share_code', strtoupper($code))->first();

        if (!$file) {
            return ['status' => 404, 'error' => '取件码无效'];
        }

        // 检查过期
        if (now()->greaterThan($file->expires_at)) {
            $this->deleteFile($file); // 触发清理
            return ['status' => 410, 'error' => '文件已过期'];
        }

        return ['status' => 200, 'file' => $file];
    }

    /**
     * 核心业务：处理下载后的逻辑 (计数 + 阅后即焚)
     */
    public function handlePostDownload(File $file)
    {
        $file->increment('download_count');
        
        // 返回是否需要阅后即焚
        return $file->is_one_time;
    }

    /**
     * 执行“软删除”：保留数据库记录，但清除物理文件和取件码
     * @param File $file
     * @param string $reason 删除原因
     */
    public function deleteFile(File $file, string $reason = 'manual')
    {
        // 1. 🗑️ 删除硬盘上的物理文件
        if (Storage::exists($file->storage_path)) {
            Storage::delete($file->storage_path);
        }

        // 2. ♻️ 归还取件码 (让它回到池子给别人用)
        $this->codeManager->recycleCode($file->share_code);

        // 3. 📝 标记数据库记录为“已删除”并记录原因
        // 我们不直接调用 $file->delete()，因为我们要先填入 reason
        $file->delete_reason = $reason;
        $file->save(); // 先保存原因
        
        $file->delete(); // 再执行软删除 (设置 deleted_at 时间戳)
    }

    /**
     * 模拟定时任务：批量清理所有过期文件
     * @return int 清理的文件数量
     */
    public function cleanAllExpired(): int
    {
        // 找出所有 expires_at 小于当前时间的文件
        $expiredFiles = File::where('expires_at', '<', now())->get();
        
        $count = 0;
        foreach ($expiredFiles as $file) {
            $this->deleteFile($file, 'expired'); // 传入原因：过期
            $count++;
        }
        
        return $count;
    }
}