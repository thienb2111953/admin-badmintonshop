<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MauSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('mau')->insert([
            ['ten_mau' => 'Grayish Beige'],
            ['ten_mau' => 'Đỏ'],
            ['ten_mau' => 'Xanh lá'],
            ['ten_mau' => 'Xanh dương'],
            ['ten_mau' => 'Vàng'],
            ['ten_mau' => 'Đen'],
            ['ten_mau' => 'Trắng'],
        ]);

        DB::table('kich_thuoc')->insert([
            ['ten_kich_thuoc' => '2U4'],
            ['ten_kich_thuoc' => '2U5'],
            ['ten_kich_thuoc' => '2U6'],

            ['ten_kich_thuoc' => '3U4'],
            ['ten_kich_thuoc' => '3U5'],
            ['ten_kich_thuoc' => '3U6'],

            ['ten_kich_thuoc' => '4U3'],
            ['ten_kich_thuoc' => '4U4'],
            ['ten_kich_thuoc' => '4U5'],
            ['ten_kich_thuoc' => '4U6'],

            ['ten_kich_thuoc' => '5U4'],
            ['ten_kich_thuoc' => '5U5'],
            ['ten_kich_thuoc' => '5U6'],

            ['ten_kich_thuoc' => '6U5'],
            ['ten_kich_thuoc' => '6U6'],
        ]);

    }
}
