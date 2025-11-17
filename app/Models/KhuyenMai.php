<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhuyenMai extends Model
{
    protected $table = 'khuyen_mai';
    protected $primaryKey = 'id_khuyen_mai';
    public $incrementing = true;

    protected $fillable = [
        'ma_khuyen_mai',
        'ten_khuyen_mai',
        'gia_tri',
        'don_vi_tinh',
        'ngay_bat_dau',
        'ngay_ket_thuc'
    ];
}
