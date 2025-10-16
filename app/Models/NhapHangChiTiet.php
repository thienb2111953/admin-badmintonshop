<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhapHangChiTiet extends Model
{
  protected $table = 'nhap_hang_chi_tiet';
  protected $primaryKey = 'id_nhap_hang_chi_tiet';
  public $incrementing = true;
  public $timestamps = true;
  protected $fillable = ['id_nhap_hang', 'id_san_pham_chi_tiet', 'so_luong', 'don_gia'];

  public function nhapHang()
  {
    return $this->belongsTo(NhapHang::class, 'id_nhap_hang', 'id_nhap_hang');
  }

  public function sanPhamChiTiet()
  {
    return $this->belongsTo(SanPhamChiTiet::class, 'id_san_pham_chi_tiet', 'id_san_pham_chi_tiet');
  }
}
