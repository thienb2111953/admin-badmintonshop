<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaChiNguoiDung extends Model
{
    protected $table = 'dia_chi_nguoi_dung';
    protected $primaryKey = 'id_dia_chi_nguoi_dung';
    public $incrementing = true;

    protected $fillable = ['id_nguoi_dung', 'ten_nguoi_dung', 'so_dien_thoai', 'dia_chi', 'email', 'mac_dinh'];

    // Người dùng sở hữu địa chỉ
    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'id_nguoi_dung');
    }
}
