<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdminService;
use App\Models\File;
use App\Services\FileManager;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * 管理员仪表盘 (统计数据)
     */
    public function index()
    {
        // 复用之前的统计逻辑 (基于软删除)
        $totalUploads = File::withTrashed()->count();
        $totalBytes = File::withTrashed()->sum('file_size');
        
        // 格式化大小
        $totalSize = $totalBytes > 1073741824 
            ? round($totalBytes/1073741824, 2).' GB' 
            : ($totalBytes > 1048576 ? round($totalBytes/1048576, 2).' MB' : round($totalBytes/1024, 2).' KB');

        $userFilesCount = File::withTrashed()->whereNotNull('user_id')->count();
        $userRatio = $totalUploads > 0 ? round(($userFilesCount / $totalUploads) * 100, 1) : 0;

        // 获取最近的5个活跃文件用于展示
        $recentFiles = File::latest()->take(5)->get();

        return view('admin.dashboard', compact('totalUploads', 'totalSize', 'userRatio', 'recentFiles'));
    }

    /**
     * 用户管理页面
     */
    public function users()
    {
        $users = $this->adminService->getAllUsers();
        return view('admin.users', compact('users'));
    }

    /**
     * 文件管理页面
     */
    public function files()
    {
        // 获取全站所有未删除的文件 (带分页)
        $files = File::latest()->paginate(15);
        return view('admin.files', compact('files'));
    }

    /**
     * 动作：封禁用户
     */
    public function toggleBan(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        
        try {
            $result = $this->adminService->toggleUserBan($request->user_id);
            return back()->with('status', $result['message']);
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => $e->getMessage()]);
        }
    }

    /**
     * 动作：强制删除文件
     */
    public function deleteFile(Request $request, FileManager $fileManager)
    {
        $file = File::findOrFail($request->id);
        $fileManager->deleteFile($file, 'admin_force_delete');
        return back()->with('status', '文件已强制删除并归档。');
    }
}