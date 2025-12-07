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
            // Associated User, allows null because guests can also upload (关联用户 允许为空 因为访客也能上传)
            // onDelete('cascade') means if the user is deleted, their files are also deleted (onDelete('cascade') 表示如果用户被删，他的文件也一起删)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
           
            // Associated Share Code: stores the assigned code (关联取件码：存储分配到的那个码)
            $table->string('share_code', 6);
           
            // Basic File Information (文件基本信息)
            $table->string('original_name'); // Original file name (原始文件名)
            $table->string('storage_path');  // Storage path on the server (服务器上的存储路径)
            $table->unsignedBigInteger('file_size'); // File size (bytes) (文件大小 (字节))
           
            // Business Logic Fields (业务逻辑字段)
            $table->integer('download_count')->default(0); // Download count (下载次数)
            $table->boolean('is_one_time')->default(false); // Whether it's self-destructing after one view (是否阅后即焚)
            $table->dateTime('expires_at'); // Expiration time (过期时间)

            // Soft Delete Core Field (软删除核心字段)
            $table->string('delete_reason')->nullable();
            $table->softDeletes();
                       
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