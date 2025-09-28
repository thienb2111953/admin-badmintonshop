<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    protected $table = 'san_pham';
    protected $primaryKey = 'id_san_pham';
    public $incrementing = true;

    protected $fillable = [
        'ma_san_pham',
        'ten_san_pham',
        'mo_ta',
        'gia_niem_yet',
        'gia_ban',
        'trang_thai',
        'id_danh_muc_thuong_hieu'
    ];

    public function danhMucThuongHieu()
    {
        return $this->belongsTo(DanhMucThuongHieu::class, 'id_danh_muc_thuong_hieu', 'id_danh_muc_thuong_hieu');
    }
}
