<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
// use Illuminate\Database\Seeder;

class ShareCodesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $codes = [];
        $count = 0;
        $maxCodes = 100000;
        
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $length = 6;

        // 循环生成码，直到达到最大数量
        while ($count < $maxCodes) {
            $rawCode = Str::random($length, $characters); 
            $code = strtoupper($rawCode);

            // 排除过于简单的模式
            
            // 排除所有字符都相同的码 (如 AAAAAA, 111111)
            if (count(array_unique(str_split($code))) <= 1) {
                continue; 
            }
            
            // 2. 排除连续重复的数字或字母序列 (如 123456, ABCDEF, 987654)
            $isSequential = false;
            $chars = str_split($code);
            for ($i = 0; $i < $length - 1; $i++) {
                // 检查数字连续性
                if (is_numeric($chars[$i]) && is_numeric($chars[$i+1])) {
                    if (abs($chars[$i+1] - $chars[$i]) === 1) {
                        $isSequential = true;
                        break;
                    }
                }
                // 检查字母连续性 (使用ASCII值)
                if (ctype_alpha($chars[$i]) && ctype_alpha($chars[$i+1])) {
                    if (abs(ord($chars[$i+1]) - ord($chars[$i])) === 1) {
                        $isSequential = true;
                        break;
                    }
                }
            }
            if ($isSequential) {
                continue;
            }
            // ----------------------------------------------------

            // 检查该码是否已在数组中，防止重复
            if (!in_array($code, $codes)) {
                $codes[] = $code;
                $count++;
            }
        }
        
        // 将筛选后的安全取件码批量插入到数据库
        $data = array_map(function ($c) {
            return ['code' => $c, 'is_used' => false, 'created_at' => now(), 'updated_at' => now()];
        }, $codes);

        //DB::table('share_codes')->insert($data);
        $data = array_map(function ($c) {
            return ['code' => $c, 'is_used' => false, 'created_at' => now(), 'updated_at' => now()];
        }, $codes);
        
        // 核心修复：将数据数组分成 1000 个一组的小块 (Chunk)，分批插入
        $chunkSize = 1000;
        $chunks = collect($data)->chunk($chunkSize);

        foreach ($chunks as $chunk) {
            // 对每个小块执行一次 INSERT 操作
            DB::table('share_codes')->insert($chunk->toArray());
        }
    }
}
