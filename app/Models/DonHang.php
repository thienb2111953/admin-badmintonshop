<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonHang extends Model
{
  protected $table = 'don_hang';
  protected $primaryKey = 'id_don_hang';
  public $incrementing = true;

  protected $fillable = ['ma_don_hang', 'id_nguoi_dung', 'tong_tien', 'trang_thai'];
}
