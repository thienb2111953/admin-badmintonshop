<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanPhamKhuyenMai extends Model
{
    protected $table = 'san_pham_khuyen_mai';
    protected $primaryKey = 'id_san_pham_khuyen_mai';
    public $incrementing = true;

    protected $fillable = [
        'id_khuyen_mai',
        'id_san_pham',
    ];
}
