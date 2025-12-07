<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShareCode extends Model
{
    use HasFactory;
    
    // 明确指定表名，因为模型名是 ShareCode，表名是 share_codes
    protected $table = 'share_codes'; 

    // 设置 $guarded 确保所有字段都可被批量赋值（除了 ID）
    protected $guarded = ['id']; 
}