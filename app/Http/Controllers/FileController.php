<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FileManager; // 引入服务
use App\Models\File;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    protected $fileManager;

    public function __construct(FileManager $fileManager)
    {
        $this->fileManager = $fileManager;
    }

    /**
     * 处理文件上传
     */
    public function store(Request $request)
    {
        // 1. 简单的请求验证 (访客5MB, 用户50MB)
        $limit = auth()->check() ? config('quickshare.upload_limits.user') : config('quickshare.upload_limits.guest');
        $maxSizeKb = $limit / 1024;

        $request->validate([
            'file' => 'required|file|max:' . $maxSizeKb,
            'is_one_time' => 'nullable|boolean',
        ]);

        try {
            // 2. 调用服务上传
            $file = $this->fileManager->uploadFile(
                $request->file('file'), 
                $request->boolean('is_one_time')
            );

            // 3. 跳转到成功页
            return redirect()->route('file.success', ['code' => $file->share_code]);

        } catch (\Exception $e) {
            return back()->withErrors(['msg' => $e->getMessage()]);
        }
    }

    /**
     * 处理文件下载
     */
    public function download(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        // 1. 调用服务查找文件
        $result = $this->fileManager->findFileByCode($request->input('code'));

        if ($result['status'] !== 200) {
            return back()->withErrors(['code' => $result['error']]);
        }

        $file = $result['file'];

        // 2. 处理计数和阅后即焚
        $shouldDelete = $this->fileManager->handlePostDownload($file);

        // 3. 发送下载响应 (关键修改点)
        $response = response()->download(Storage::path($file->storage_path), $file->original_name);

        // 4. 如果是阅后即焚，发送后删除
        if ($shouldDelete) {
            $response->deleteFileAfterSend(true);
            
            $file->delete();
            // 回收验证码
            app(\App\Services\CodeManager::class)->recycleCode($file->share_code);
        }

        return $response;
    }
    
    /**
     * 显示上传成功页
     */
    public function success($code)
    {
        $file = File::where('share_code', $code)->firstOrFail();
        return view('upload_success', compact('file'));
    }
}