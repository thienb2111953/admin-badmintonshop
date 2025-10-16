<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThanhToan extends Model
{
  protected $table = 'thanh_toan';
  protected $primaryKey = 'id_thanh_toan';
  public $incrementing = true;

  protected $fillable = ['id_don_hang', 'so_tien', 'ten_ngan_hang', 'ngay_thanh_toan'];
}
