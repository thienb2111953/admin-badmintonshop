<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonHang extends Model
{
  protected $table = 'don_hang';
  protected $primaryKey = 'id_don_hang';
  public $incrementing = true;

  protected $fillable = ['ma_don_hang', 'id_nguoi_dung', 'tong_tien', 'trang_thai'];


    public function diaChiGiaoHang()
    {
        return $this->belongsTo(DiaChiNguoiDung::class, 'id_dia_chi_nguoi_dung');
    }

    // Chi tiết đơn hàng
    public function chiTiet()
    {
        return $this->hasMany(DonHangChiTiet::class, 'id_don_hang');
    }

    // Người dùng đặt hàng
    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'id_nguoi_dung');
    }


}
