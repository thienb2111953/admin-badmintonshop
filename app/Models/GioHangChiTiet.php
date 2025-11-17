<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GioHangChiTiet extends Model
{
    protected $table = 'gio_hang_chi_tiet';
    protected $primaryKey = 'id_gio_hang_chi_tiet';
    public $incrementing = true;

    protected $fillable = [
        'id_gio_hang',
        'id_san_pham_chi_tiet',
        'so_luong',
    ];
}
