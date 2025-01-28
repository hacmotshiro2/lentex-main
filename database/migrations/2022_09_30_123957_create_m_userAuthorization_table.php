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
        if(Schema::hasTable('m_userauthorization')){return;}
        Schema::create('m_userauthorization', function (Blueprint $table) {
            $table->collation = 'utf8mb4_general_ci';

            $table->id();
            $table->unsignedBigInteger('user_id')->unsigned();
            $table->string('description',255)->nullable();

            $table->timestamps();
            $table->softDeletes();
            
            //外部キーの設定
            $table->foreign('user_id')->references('id')->on('users')->onDeletes('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_userauthorization');
    }
};
