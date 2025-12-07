<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CodeManager;
use App\Services\FileManager;
use App\Models\ShareCode;
use App\Models\File;
use App\Models\FileLog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Services\AdminService;
use App\Models\User;

class TestController extends Controller
{
    // 显示调试台
    public function index()
    {
        return view('test_debug');
    }

    // ------------------------------------------------
    // 区域 1: 取件码调试
    // ------------------------------------------------
    public function getCode(CodeManager $manager)
    {
        $code = $manager->getNextAvailableCode();
        $dbRecord = $code ? ShareCode::where('code', $code)->first() : null;
        
        return view('test_debug', [
            'section' => 'code',
            'action' => 'get',
            'code' => $code,
            'dbRecord' => $dbRecord
        ]);
    }

    public function recycleCode(Request $request, CodeManager $manager)
    {
        $code = $request->input('code');
        $manager->recycleCode($code);
        $dbRecord = ShareCode::where('code', $code)->first();

        return view('test_debug', [
            'section' => 'code',
            'action' => 'recycle',
            'code' => $code,
            'dbRecord' => $dbRecord
        ]);
    }

    // ------------------------------------------------
    // 区域 2: 文件上传/下载调试
    // ------------------------------------------------
    
    // 动作：上传文件
    public function uploadTest(Request $request, FileManager $fileManager)
    {
        // 稍微调大限制方便测试 (50MB)
        $request->validate(['file' => 'required|file|max:51200']); 

        try {
            $file = $fileManager->uploadFile($request->file('file'));
            
            return view('test_debug', [
                'section' => 'file',
                'action' => 'upload',
                'fileRecord' => $file
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => $e->getMessage()]);
        }
    }

    // 动作：下载文件
    public function downloadTest(Request $request, FileManager $fileManager)
    {
        $code = $request->input('code');
        
        $result = $fileManager->findFileByCode($code);

        if ($result['status'] !== 200) {
            return back()->withErrors(['download_msg' => $result['error']]);
        }

        $file = $result['file'];
        
        // 增加计数
        $file->increment('download_count');

        return Storage::download($file->storage_path, $file->original_name);
    }

    // ------------------------------------------------
    // 区域 3: 管理员监控 & 统计
    // ------------------------------------------------

    /**
     * 显示所有被占用的取件码 + 📊 系统统计数据
     */
    public function listOccupiedCodes()
    {
        $occupiedCodes = ShareCode::where('is_used', true)
                                  ->orderBy('updated_at', 'desc')
                                  ->get();

        // --- 📊 统计数据 (使用软删除 API) ---
        
        // 1. 总上传量 (包含已删除的历史记录)
        $totalUploads = File::withTrashed()->count();
        
        // 2. 累计流量 (包含已删除的文件大小)
        $totalBytes = File::withTrashed()->sum('file_size');
        $totalSize = $totalBytes > 1048576 ? round($totalBytes/1048576, 2).' MB' : round($totalBytes/1024, 2).' KB';

        // 3. 用户占比 (在所有历史记录中统计)
        $userFilesCount = File::withTrashed()->whereNotNull('user_id')->count();
        $guestFilesCount = $totalUploads - $userFilesCount;
        
        $userRatio = $totalUploads > 0 ? round(($userFilesCount / $totalUploads) * 100, 1) : 0;

        return view('test_debug', [
            // ... 视图返回参数保持不变 ...
            'section' => 'admin',
            'action' => 'list',
            'occupiedCodes' => $occupiedCodes,
            'stats' => [
                'total_uploads' => $totalUploads,
                'total_size' => $totalSize,
                'user_ratio' => $userRatio,
                'guest_count' => $guestFilesCount,
                'user_count' => $userFilesCount
            ]
        ]);
    }
    /**
     * 强制删除文件 (手动单点删除)
     */
    public function manualDelete(Request $request, FileManager $fileManager)
    {
        $code = $request->input('code');
        
        $file = \App\Models\File::where('share_code', $code)->first();

        if ($file) {
            // 调用服务进行彻底删除 (会自动归档)
            $fileManager->deleteFile($file, 'manual_admin');
            $message = "文件 (Code: $code) 已成功删除并归档，取件码已回收。";
        } else {
            // 修复孤儿码
            app(CodeManager::class)->recycleCode($code);
            $message = "未找到活跃文件记录，已强制回收取件码 ($code)。";
        }

        return $this->listOccupiedCodes()->with('status', $message);
    }

    /**
     * 动作：触发一次“定时清理任务” (批量)
     */
    public function triggerCleanup(FileManager $fileManager)
    {
        $count = $fileManager->cleanAllExpired();

        return $this->listOccupiedCodes()->with('status', "维护任务执行完毕：共清理并归档了 $count 个过期文件。");
    }

    // ------------------------------------------------
    // 区域 4: 用户管理 (封禁系统)
    // ------------------------------------------------

    /**
     * 动作：加载用户列表
     */
    public function listUsers(AdminService $adminService)
    {
        // 调用 AdminService 获取数据
        $users = $adminService->getAllUsers();

        return view('test_debug', [
            'section' => 'users', // 激活用户面板
            'action' => 'list',
            'users' => $users,
            // 为了防止页面其他部分报错，传空值
            'occupiedCodes' => collect([]), 
            'stats' => null
        ]);
    }

    /**
     * 动作：执行封禁/解封
     */
    public function debugToggleBan(Request $request, AdminService $adminService)
    {
        $request->validate(['user_id' => 'required']);

        try {
            // 调用服务处理
            $result = $adminService->toggleUserBan($request->user_id);
            
            // 重定向回列表页，并带上成功消息
            return redirect()->route('debug.users.list')->with('status', $result['message']);

        } catch (\Exception $e) {
            return back()->withErrors(['msg' => $e->getMessage()]);
        }
    }

    // ------------------------------------------------
    // 区域 5: 邮件服务测试
    // ------------------------------------------------

    public function sendTestEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $targetEmail = $request->input('email');

        try {
            // 发送一封纯文本测试邮件
            Mail::raw("QuickShare测试邮件\n发送时间：" . now(), function ($message) use ($targetEmail) {
                $message->to($targetEmail)
                        ->subject('QuickShare SMTP 连接测试');
            });

            return back()->with('status', "✅ 测试邮件已成功发送至: $targetEmail (请检查收件箱或垃圾邮件)");

        } catch (\Exception $e) {
            return back()->withErrors(['msg' => "❌ 邮件发送失败: " . $e->getMessage()]);
        }
    }
}