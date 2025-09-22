<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanPhamTonKho extends Model
{
    protected $table = 'san_pham_ton_kho';
    protected $primaryKey = 'id_san_pham_ton_kho';
    public $incrementing = true;

    protected $fillable = [
        'id_san_pham_chi_tiet',
        'so_luong_ton',
    ];

    public function sanPhamChiTiet()
    {
        return $this->belongsTo(AnhSanPham::class, 'id_san_pham_ton_kho', 'id_san_pham_ton_kho');
    }
}
