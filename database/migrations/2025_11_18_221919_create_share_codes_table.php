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
        Schema::create('share_codes', function (Blueprint $table) {
            $table->id();
                       
            // 6-digit Share Code (6位取件码)
            $table->string('code', 6)->unique();
                               
            // Status: true means being used by a file, false means idle/available (状态: true表示正在被某个文件使用 false表示空闲可分配)
            $table->boolean('is_used')->default(false);
                       
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('share_codes');
    }
};