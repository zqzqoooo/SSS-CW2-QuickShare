<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileLog extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    
    // 依然可以定义关联，方便管理员查看“某用户的所有历史”
    public function user() {
        return $this->belongsTo(User::class);
    }
}