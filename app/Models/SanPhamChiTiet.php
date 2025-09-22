<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanPhamChiTiet extends Model
{
    protected $table = 'san_pham_chi_tiet';
    protected $primaryKey = 'id_san_pham_chi_tiet';
    public $incrementing = true;

    protected $fillable = [
        'id_san_pham',
        'ten_mau',
        'ten_kich_thuoc',
        'so_luong_ton'
    ];

    public function anhSanPham()
    {
        return $this->hasMany(AnhSanPham::class, 'id_san_pham_chi_tiet', 'id_san_pham_chi_tiet');
    }

    public function tonKho()
    {
        return $this->hasOne(SanPhamTonKho::class, 'id_san_pham_chi_tiet', 'id_san_pham_chi_tiet');
    }
}
