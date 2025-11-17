<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonHangKhuyenMai extends Model
{
    protected $table = 'don_hang_khuyen_mai';
    protected $primaryKey = 'id_don_hang_khuyen_mai';
    public $incrementing = true;

    protected $fillable = [
        'id_khuyen_mai',
        'gia_tri_duoc_giam',
    ];
}
