<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. 引入 Trait

class File extends Model
{
    use HasFactory, SoftDeletes; // 2. 启用 SoftDeletes
    
    // 设置 $guarded 确保所有字段都可被批量赋值（除了 ID），以简化上传逻辑
    protected $guarded = ['id']; 
    
    // 明确指出我们不使用 Laravel 的默认时间戳（虽然我们有，但为了清晰演示）
    // public $timestamps = true; 
    
    // 👇👇 新增部分：告诉 Laravel 这是一个日期字段 👇👇
    protected $casts = [
        'expires_at' => 'datetime',
        'is_one_time' => 'boolean', // 顺便把这个也转为布尔值，好习惯
    ];
    // 👆👆 新增结束 👆👆

    /**
     * 定义 File 和 User 的逆向关系：一个文件属于一个用户 (BelongsTo)
     */
    public function user(): BelongsTo
    {
        // 外键 user_id 关联到 User 模型
        return $this->belongsTo(User::class);
    }
    
}