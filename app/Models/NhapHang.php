<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhapHang extends Model
{
  protected $table = 'nhap_hang';
  protected $primaryKey = 'id_nhap_hang';
  public $incrementing = true;
  public $timestamps = true;
  protected $fillable = ['ma_nhap_hang', 'ngay_nhap'];

  public function chiTiet()
  {
    return $this->hasMany(NhapHangChiTiet::class, 'id_nhap_hang', 'id_nhap_hang');
  }
}
