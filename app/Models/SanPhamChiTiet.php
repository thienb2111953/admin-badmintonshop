<?php

namespace App\Models;

use App\Http\Controllers\Admin\KhoController;
use App\Http\Controllers\Admin\MauController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SanPhamChiTiet extends Model
{
  protected $table = 'san_pham_chi_tiet';
  protected $primaryKey = 'id_san_pham_chi_tiet';
  public $incrementing = true;

  protected $fillable = ['id_san_pham', 'id_mau', 'id_kich_thuoc', 'ten_san_pham_chi_tiet', 'so_luong_ton','gia_niem_yet', 'gia_ban'];

  public function anhSanPham(): HasMany
  {
    return $this->hasMany(AnhSanPham::class, 'id_san_pham_chi_tiet', 'id_san_pham_chi_tiet');
  }

  public function mau(): BelongsTo
  {
    return $this->belongsTo(Mau::class, 'id_mau', 'id_mau');
  }

  public function kichThuoc(): BelongsTo
  {
    return $this->belongsTo(KichThuoc::class, 'id_kich_thuoc', 'id_kich_thuoc');
  }

    public function nhapHangChiTiet()
    {
        return $this->hasMany(NhapHangChiTiet::class, 'id_san_pham_chi_tiet');
    }

}
