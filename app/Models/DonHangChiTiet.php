<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonHangChiTiet extends Model
{
  protected $table = 'don_hang_chi_tiet';
  protected $primaryKey = 'id_don_hang_chi_tiet';
  public $incrementing = true;

  protected $fillable = ['id_don_hang', 'id_san_pham_chi_tiet', 'so_luong', 'don_gia', 'tong_tien'];

    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'id_don_hang');
    }

    public function sanPhamChiTiet()
    {
        return $this->belongsTo(SanPhamChiTiet::class, 'id_san_pham_chi_tiet');
    }

}
