<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_len_course', function (Blueprint $table) {
            $table->collation = 'utf8mb4_general_ci';
        
            $table->id(); // 自動的に主キーを作成
            $table->string('courseName', 128); // コース名
            $table->timestamps(); // created_at, updated_atを自動生成
            $table->softDeletes(); // deleted_atを生成 (ソフトデリート)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_len_course');
    }
};
