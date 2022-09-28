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
        Schema::create('m_len_lineusers', function (Blueprint $table) {
            $table->collation = 'utf8mb4_general_ci';

            $table->id();
            $table->integer('student_id')->unsigned();
            $table->string('lineDisplayName',255);
            $table->string('lineUserId',255);
            $table->timestamps();
            $table->softDeletes();

            //外部キーの設定
            $table->foreign('student_id')->references('id')->on('m_len_students')->onDeletes('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_len_lineusers');
    }
};
