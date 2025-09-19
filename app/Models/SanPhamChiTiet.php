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
        'id_mau',
        'id_kich_thuoc',
        'id_anh_san_pham',
    ];

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'id_san_pham', 'id_san_pham');
    }

    public function mau()
    {
        return $this->hasMany(Mau::class, 'id_mau', 'id_mau');
    }
}
