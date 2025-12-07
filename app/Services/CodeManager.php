<?php

namespace App\Services;

use App\Models\ShareCode;
use Illuminate\Support\Facades\DB;

class CodeManager
{
    /**
     * Retrieves the next available secure share code and marks it as used.
     * This is an advanced operation using transactions and row locking (lockForUpdate) to prevent concurrency errors.
     * (获取下一个可用的安全取件码，并将其标记为已使用。
     * 这是一个高级操作，使用了事务和行锁 (lockForUpdate) 来防止并发错误。)
     * * @return string|null
     */
    public function getNextAvailableCode(): ?string
    {
        $codeRecord = DB::transaction(function () {
            $totalCodes = ShareCode::where('is_used', false)->count();

            // 1. If the number of available codes is 0, return null immediately (如果可用码数量为 0，直接返回 null)
            if ($totalCodes === 0) {
                return null;
            }
            
            // 2. Determine a random starting offset (确定随机起始点)
            $offset = random_int(0, $totalCodes - 1);
            
            // 3. Core Logic: Search for the first unused code starting from the random offset, and apply a lock (核心逻辑：从随机点开始查找第一个未使用的码，并加锁)
            $code = ShareCode::where('is_used', false)
                             ->offset($offset)
                             ->orderBy('id') 
                             ->lockForUpdate() // Apply lock to prevent concurrency issues (加锁防止并发问题)
                             ->first();

            // 4. If nothing is found from the random offset to the end, search from the beginning (如果随机点到结尾找不到，则从头开始搜索)
            if (!$code) {
                $code = ShareCode::where('is_used', false)
                                 ->limit($offset)
                                 ->orderBy('id')
                                 ->lockForUpdate() 
                                 ->first();
            }
            
            // 5. If a code is found, mark it as used (找到码则标记为已使用)
            if ($code) {
                $code->is_used = true;
                $code->save();
            }

            return $code;
        });

        // Finally return the share code string (最终返回取件码字符串)
        return $codeRecord ? $codeRecord->code : null;
    }

    /**
     * Recycles the share code, setting its status to unused so it can be returned to the pool.
     * (Used by scheduled cleanup tasks)
     * (回收取件码，将其状态设为未使用，以便重新放回码池。
     * (供定时清理任务使用))
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