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
        Schema::create('r_len_entexhistory', function (Blueprint $table) {
            $table->collation = 'utf8mb4_general_ci';

            $table->id();
            $table->integer('student_id');
            $table->tinyinteger('type');
            $table->string('LearningRoomCd',6);
            $table->datetime('entex_datetime');    
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('r_len_entexhistory');
    }
};
