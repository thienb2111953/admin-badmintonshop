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

    }
}
