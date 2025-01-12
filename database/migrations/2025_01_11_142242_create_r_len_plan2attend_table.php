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
        Schema::create('r_len_plan2attend', function (Blueprint $table) {
            $table->collation = 'utf8mb4_general_ci';
        
            $table->id(); // 自動的に主キーを作成
            $table->foreignId('session_id')->constrained('r_len_session')->onDelete('cascade'); // 外部キー制約
            $table->foreignId('student_id')->constrained('m_len_students')->onDelete('cascade'); // 外部キー制約
            $table->string('description', 128)->nullable();
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
        Schema::dropIfExists('r_len_plan2attend');
    }
};
