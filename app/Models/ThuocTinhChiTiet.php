<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThuocTinhChiTiet extends Model
{
    protected $table = 'thuoc_tinh_chi_tiet';
    protected $primaryKey = 'id_thuoc_tinh_chi_tiet';
    public $incrementing = true;

    protected $fillable = [
        'ten_thuoc_tinh_chi_tiet',
        'id_thuoc_tinh',
    ];

    public function thuocTinh()
    {
        return $this->belongsTo(ThuocTinh::class, 'id_thuoc_tinh', 'id_thuoc_tinh');
    }
}
