<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SanPhamThuocTinh extends Model
{
    protected $table = 'san_pham_thuoc_tinh';
    protected $primaryKey = 'id_san_pham_thuoc_tinh';
    public $incrementing = true;

    protected $fillable = ['id_san_pham', 'id_thuoc_tinh_chi_tiet'];

    public function sanPham(): BelongsTo
    {
        return $this->belongsTo(SanPham::class, 'id_san_pham', 'id_san_pham');
    }

    public function thuocTinhChiTiet(): BelongsTo
    {
        return $this->belongsTo(ThuocTinhChiTiet::class, 'id_thuoc_tinh_chi_tiet', 'id_thuoc_tinh_chi_tiet');
    }

}
