<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // user name (用户名)
            $table->string('email')->unique(); // user email (用户邮箱)
            $table->timestamp('email_verified_at')->nullable(); // email verification time (邮箱验证时间)
            $table->string('password'); // user password (用户密码)
                        
            $table->boolean('is_admin')->default(false); // Whether the user is an administrator (是否为管理员)
            $table->boolean('is_banned')->default(false); // Whether the user is banned (是否被封禁)
                       
            $table->rememberToken(); // remember me token (记住我令牌)
            $table->timestamps(); // created_at and updated_at timestamps (创建和更新的时间戳)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};