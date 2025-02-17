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
        Schema::table('r_len_entexhistory', function (Blueprint $table) {
            //
            $table->bigInteger('session_id')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('r_len_entexhistory', function (Blueprint $table) {
            //
            $table->dropColumn('session_id');

        });
    }
};
