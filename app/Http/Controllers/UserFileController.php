<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\CodeManager;
use App\Models\File;

class UserFileController extends Controller
{
    /**
     * 获取我的文件列表 (页面)
     */
    public function index()
    {
        // 获取当前用户文件，按上传时间倒序
        $files = Auth::user()->files()->latest()->get();
        return view('dashboard', compact('files'));
    }

    /**
     * 核心功能：在线预览文件 (图片/音视频)
     * 这是一个“流式”响应，允许浏览器直接渲染内容
     */
    public function preview($id)
    {
        $file = Auth::user()->files()->findOrFail($id);

        // 检查文件是否存在
        if (!Storage::exists($file->storage_path)) {
            abort(404);
        }

        // 使用 Laravel 的 file() 响应，它会自动设置正确的 Content-Type
        return response()->file(Storage::path($file->storage_path));
    }

    /**
     * 更新操作：主要用于“延期”
     */
    public function update(Request $request, $id)
    {
        $file = Auth::user()->files()->findOrFail($id);

        // 1. 处理延期逻辑
        if ($request->has('extend')) {
            $file->expires_at = $file->expires_at->addDays(3);
            $file->save();
            return back()->with('status', '✅ File expiration successfully extended by 3 days');
        }

        // 2. 处理重命名逻辑 (修复点：增加了这段逻辑)
        if ($request->has('filename')) {
            $request->validate(['filename' => 'required|string|max:255']);
            $file->original_name = $request->input('filename');
            $file->save();
            return back()->with('status', '✅ File name updated successfully!');
        }

        return back();
    }

    /**
     * 显示文件详情页 (预览 + 重命名 + 管理)
     */
    public function show($id)
    {
        $file = Auth::user()->files()->findOrFail($id);
        return view('file_detail', compact('file'));
    }

    /**
     * 删除文件
     */
    public function destroy($id, CodeManager $codeManager)
    {
        $file = Auth::user()->files()->findOrFail($id);

        // 1. 物理删除
        if (Storage::exists($file->storage_path)) {
            Storage::delete($file->storage_path);
        }
        
        // 2. 回收取件码
        $codeManager->recycleCode($file->share_code);

        // 3. 软删除记录
        $file->delete();

    // 使用 redirect()->route('dashboard') 明确指定跳回列表页
    return redirect()->route('dashboard')->with('status', '🗑️ File has been deleted.');    }
}