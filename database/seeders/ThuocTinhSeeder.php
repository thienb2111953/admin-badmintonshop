<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ThuocTinh;
use App\Models\ThuocTinhChiTiet;

class ThuocTinhSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Chiều dài vợt' => ['665 mm', '670 mm', '675 mm'],
            'Chiều dài cán vợt' => ['200 mm', '205 mm', '210 mm'],
            'Swingweight' => ['Dưới 82 kg/cm2', '82-84 kg/cm2', '84-86 kg/cm2', '86-88 kg/cm2', 'Trên 88 kg/cm2'],
            'Trọng lượng' => ['2U: 90 - 94g', '3U: 85 - 89g', '4U: 80 - 84g', '5U: 75 - 79g', 'F: 70 - 74g'],
            'Điểm cân bằng' => ['Nhẹ đầu', 'Cân bằng', 'Hơi nặng đầu', 'Nặng đầu', 'Siêu nặng đầu'],
            'Độ cứng đũa' => ['Dẻo', 'Trung bình', 'Cứng', 'Siêu cứng'],
            'Phong cách chơi' => ['Tấn công', 'Công thủ toàn diện', 'Phản tạt, phòng thủ'],
            'Nội dung chơi' => ['Đánh đơn', 'Đánh đôi', 'Cả đơn và đôi'],
            'Trình độ chơi' => ['Mới chơi', 'Trung bình'],
        ];

        foreach ($data as $tenThuocTinh => $chiTiets) {
            $thuocTinh = ThuocTinh::create([
                'ten_thuoc_tinh' => $tenThuocTinh,
            ]);

            foreach ($chiTiets as $tenChiTiet) {
                ThuocTinhChiTiet::create([
                    'id_thuoc_tinh' => $thuocTinh->id_thuoc_tinh,
                    'ten_thuoc_tinh_chi_tiet' => $tenChiTiet,
                ]);
            }
        }
    }
}
