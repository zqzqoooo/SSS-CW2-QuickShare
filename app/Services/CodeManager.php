<?php

namespace App\Services;

use App\Models\ShareCode;
use Illuminate\Support\Facades\DB;

class CodeManager
{
    /**
     * 获取下一个可用的安全取件码，并将其标记为已使用。
     * 这是一个高级操作，使用了事务和行锁 (lockForUpdate) 来防止并发错误。
     * * @return string|null
     */
    public function getNextAvailableCode(): ?string
    {
        $codeRecord = DB::transaction(function () {
            $totalCodes = ShareCode::where('is_used', false)->count();

            // 1. 如果可用码数量为 0，直接返回 null
            if ($totalCodes === 0) {
                return null;
            }
            
            // 2. 确定随机起始点
            // PHP 的 random_int() 用于生成更安全的随机数
            $offset = random_int(0, $totalCodes - 1);
            
            // 3. 核心逻辑：从随机点开始查找第一个未使用的码，并加锁
            // 我们使用 limit(1) 和 offset 来定位到随机点
            $code = ShareCode::where('is_used', false)
                             ->offset($offset)
                             ->orderBy('id') 
                             ->lockForUpdate() // 加锁防止并发问题
                             ->first();

            // 4. 如果随机点到结尾找不到，则从头开始搜索
            if (!$code) {
                $code = ShareCode::where('is_used', false)
                                 ->limit($offset) // 搜索随机点之前的部分
                                 ->orderBy('id')
                                 ->lockForUpdate() 
                                 ->first();
            }
            
            // 5. 找到码则标记为已使用
            if ($code) {
                $code->is_used = true;
                $code->save();
            }

            // 如果执行到这里 $code 仍为 null，说明码池可能在事务开始时被清空，但我们已在第1步检查
            return $code;
        });

        // 最终返回取件码字符串，如果找不到则返回 null (对应“系统错误”状态)
        return $codeRecord ? $codeRecord->code : null;
    }

    /**
     * 回收取件码，将其状态设为未使用，以便重新放回码池。
     * (供定时清理任务使用)
     * * @param string $code
     * @return bool
     */
    public function recycleCode(string $code): bool
    {
        $codeRecord = ShareCode::where('code', $code)->first();

        if ($codeRecord) {
            $codeRecord->is_used = false;
            return $codeRecord->save();
        }
        
        return false;
    }
}
