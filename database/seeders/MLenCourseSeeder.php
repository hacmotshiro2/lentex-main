<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class MLenCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('m_len_course')->insert([
            [
                'id' => 10,
                'courseName' => 'ビギナーコース',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 20,
                'courseName' => 'ゼネラルコース',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 30,
                'courseName' => 'プロフェッショナルコース',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
