<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 确保 Admin 用户不存在，以维持幂等性
        if (User::where('email', 'admin@admin.com')->doesntExist()) {
            
            User::create([
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('admin'),
                'is_admin' => true,
                'is_banned' => false,
            ]);  
        }
    }
}
