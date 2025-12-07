<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminService
{
    /**
     * 获取所有用户列表 (排除当前管理员自己)
     */
    public function getAllUsers()
    {
        return User::where('id', '!=', Auth::id())->get();
    }

    /**
     * 执行封禁/解封逻辑
     * @param int $userId
     * @return array 返回操作结果状态和消息
     */
    public function toggleUserBan(int $userId): array
    {
        $user = User::findOrFail($userId);

        // 安全检查：防止封禁管理员
        if ($user->is_admin) {
            throw new \Exception('无法封禁管理员账号');
        }

        // 切换状态
        $user->is_banned = !$user->is_banned;
        $user->save();

        $status = $user->is_banned ? '封禁' : '解封';

        return [
            'user' => $user,
            'message' => "用户 {$user->name} 已成功{$status}。",
            'is_banned' => $user->is_banned
        ];
    }
}
