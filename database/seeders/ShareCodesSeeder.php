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
     * Run the database seeds. (运行数据库填充器)
     */
    public function run(): void
    {
        $codes = [];
        $count = 0;
        $maxCodes = 100000;
        
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $length = 6;

        // Loop to generate codes until the maximum quantity is reached (循环生成码，直到达到最大数量)
        while ($count < $maxCodes) {
            $rawCode = Str::random($length, $characters); 
            $code = strtoupper($rawCode);

            // 1. Exclude codes where all characters are identical (排除所有字符都相同的码)
            if (count(array_unique(str_split($code))) <= 1) {
                continue; 
            }
            
            // 2. Exclude codes with consecutive repeating number or letter sequences (排除连续重复的数字或字母序列)
            $isSequential = false;
            $chars = str_split($code);
            for ($i = 0; $i < $length - 1; $i++) {
                // Check for numeric sequence (检查数字连续性)
                if (is_numeric($chars[$i]) && is_numeric($chars[$i+1])) {
                    if (abs($chars[$i+1] - $chars[$i]) === 1) {
                        $isSequential = true;
                        break;
                    }
                }
                // Check for alphabetical sequence (检查字母连续性)
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
            
            // Check if the code is already in the array to prevent duplication (检查该码是否已在数组中，防止重复)
            if (!in_array($code, $codes)) {
                $codes[] = $code;
                $count++;
            }
        }
        
        // Batch insert the filtered secure share codes into the database (将筛选后的安全取件码批量插入到数据库)
        $data = array_map(function ($c) {
            return ['code' => $c, 'is_used' => false, 'created_at' => now(), 'updated_at' => now()];
        }, $codes);

        // Chunk the data array into smaller pieces of 1000 (将数据数组分成 1000 个一组的小块 (Chunk)，分批插入)
        $chunkSize = 1000;
        $chunks = collect($data)->chunk($chunkSize);

        foreach ($chunks as $chunk) {
            // Execute one INSERT operation for each chunk (对每个小块执行一次 INSERT 操作)
            DB::table('share_codes')->insert($chunk->toArray());
        }
    }
}