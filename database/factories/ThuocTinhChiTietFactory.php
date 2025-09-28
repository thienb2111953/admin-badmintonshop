<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ThuocTinhChiTiet>
 */
class ThuocTinhChiTietFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ten_thuoc_tinh_chi_tiet' => $this->faker->word(),
            // 'id_thuoc_tinh' sẽ tự được gán nhờ has()
        ];
    }
}
