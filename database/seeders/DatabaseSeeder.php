<?php

namespace Database\Seeders;

use App\Models\ThuocTinh;
use App\Models\ThuocTinhChiTiet;
use App\Models\ThuongHieu;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
        ]);

        ThuocTinh::factory(5)->create()->each(function ($thuocTinh) {
            $thuocTinh->chiTiets()->createMany(
                ThuocTinhChiTiet::factory(3)->make()->toArray()
            );
        });
    }
}
