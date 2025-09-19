<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KichThuoc extends Model
{
    protected $table = 'kich_thuoc';
    protected $primaryKey = 'id_kich_thuoc';
    public $incrementing = true;

    protected $fillable = [
        'ten_kich_thuoc',
    ];

    public function sanPhamChiTiet()
    {
        return $this->hasMany(SanPhamChiTiet::class, 'id_kich_thuoc', 'id_kich_thuoc');
    }
}
