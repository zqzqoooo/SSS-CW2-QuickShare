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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            // 1. 关联用户：允许为空 (nullable)，因为访客也能上传
            // onDelete('cascade') 表示如果用户被删，他的文件也一起删
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
           
            // 2. 关联取件码：存储分配到的那个码
            $table->string('share_code', 6);
           
            // 3. 文件基本信息
            $table->string('original_name'); // 原始文件名 (如 report.pdf)
            $table->string('storage_path');  // 服务器上的存储路径 (如 uploads/xxx.pdf)
            $table->unsignedBigInteger('file_size'); // 文件大小 (字节)
           
            // 4. 业务逻辑字段
            $table->integer('download_count')->default(0); // 下载次数
            $table->boolean('is_one_time')->default(false); // 是否阅后即焚
            $table->dateTime('expires_at'); // 过期时间 (核心字段)

            // --- 新增：软删除核心字段 ---
            $table->string('delete_reason')->nullable(); // 记录：过期/手动/阅后即焚
            $table->softDeletes(); // 这会自动创建 deleted_at 字段
            // -------------------------
                       
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
