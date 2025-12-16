<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YeuCauBaoHanh extends Model
{
    use HasFactory;

    protected $table = 'yeu_cau_bao_hanh';

    protected $primaryKey = 'id_yeu_cau_bao_hanh';

    // 3. Các trường cho phép thêm dữ liệu
    protected $fillable = [
        'id_nguoi_dung',
        'customer_name',
        'customer_phone',
        'customer_email',
        'ma_don_hang',
        'description',
        'attachment',
        'status',
    ];

    // 4. Tự động chuyển đổi JSON sang Array khi lấy dữ liệu và ngược lại
    protected $casts = [
        'attachment' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
