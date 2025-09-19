<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhMucThuongHieu extends Model
{
    protected $table = 'danh_muc_thuong_hieu';
    protected $primaryKey = 'id_danh_muc_thuong_hieu';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'id_thuong_hieu',
        'id_danh_muc',
        'ten_danh_muc_thuong_hieu',
        'mo_ta',
        'slug',
    ];

    public function danhMuc()
    {
        return $this->belongsTo(DanhMuc::class, 'id_danh_muc', 'id_danh_muc');
    }

    public function thuongHieu()
    {
        return $this->belongsTo(ThuongHieu::class, 'id_thuong_hieu', 'id_thuong_hieu');
    }

    public function sanPham()
    {
        return $this->hasMany(SanPham::class, 'id_danh_muc_thuong_hieu', 'id_danh_muc_thuong_hieu');
    }
}
