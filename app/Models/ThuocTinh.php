<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThuocTinh extends Model
{
    protected $table = 'thuoc_tinh';
    protected $primaryKey = 'id_thuoc_tinh';
    public $incrementing = true;

    protected $fillable = [
        'ten_thuoc_tinh',
    ];

    public function chiTiets()
    {
        return $this->hasMany(ThuocTinhChiTiet::class, 'id_thuoc_tinh', 'id_thuoc_tinh');
    }

    // Quan hệ nhiều-nhiều với DanhMuc qua bảng pivot
    public function danhMucs()
    {
        return $this->belongsToMany(
            DanhMuc::class,
            'danh_muc_thuoc_tinh',
            'id_thuoc_tinh',
            'id_danh_muc'
        )->withPivot('id_danh_muc_thuoc_tinh')->withTimestamps();
    }
}
