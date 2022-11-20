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
        Schema::table('m_len_students', function (Blueprint $table) {
            //
            $table->string('lsuppoStudentCd',8)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('m_len_students', function (Blueprint $table) {
            //
            $table->dropColumn('lsuppoStudentCd');

        });
    }
};
