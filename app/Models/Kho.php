<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kho extends Model
{
    protected $table = 'kho';
    protected $primaryKey = 'id_kho';
    public $incrementing = true;

    protected $fillable = [
        'id_san_pham_chi_tiet',
        'so_luong_nhap',
        'ngay_nhap',
    ];

    public function sanPhamChiTiet(): BelongsTo
    {
        return $this->belongsTo(SanPhamChiTiet::class, 'id_san_pham_chi_tiet', 'id_san_pham_chi_tiet');
    }
}
