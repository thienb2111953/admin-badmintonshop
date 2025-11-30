<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KichThuocSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

            ['ten_kich_thuoc' => 'S'],
            ['ten_kich_thuoc' => 'M'],
            ['ten_kich_thuoc' => 'L'],
            ['ten_kich_thuoc' => 'XL'],
            ['ten_kich_thuoc' => '2XL'],
            ['ten_kich_thuoc' => '3XL'],

            ['ten_kich_thuoc' => '30'],
            ['ten_kich_thuoc' => '31'],
            ['ten_kich_thuoc' => '32'],
            ['ten_kich_thuoc' => '33'],
            ['ten_kich_thuoc' => '34'],
            ['ten_kich_thuoc' => '35'],
            ['ten_kich_thuoc' => '36'],
            ['ten_kich_thuoc' => '37'],
            ['ten_kich_thuoc' => '37.5'],
            ['ten_kich_thuoc' => '38'],
            ['ten_kich_thuoc' => '38.5'],
            ['ten_kich_thuoc' => '39'],
            ['ten_kich_thuoc' => '39.5'],
            ['ten_kich_thuoc' => '40'],
            ['ten_kich_thuoc' => '40.5'],
            ['ten_kich_thuoc' => '41'],
            ['ten_kich_thuoc' => '41.5'],
            ['ten_kich_thuoc' => '42'],
            ['ten_kich_thuoc' => '42.5'],
            ['ten_kich_thuoc' => '43'],
            ['ten_kich_thuoc' => '43.5'],
            ['ten_kich_thuoc' => '44'],
            ['ten_kich_thuoc' => '45'],
            ['ten_kich_thuoc' => '45.5'],
        ]);
    }
}
