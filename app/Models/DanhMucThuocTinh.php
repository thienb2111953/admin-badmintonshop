<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhMucThuocTinh extends Model
{
    protected $table = 'danh_muc_thuoc_tinh';
    protected $primaryKey = 'id_danh_muc_thuoc_tinh';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'id_thuoc_tinh',
        'id_danh_muc',
    ];

    public function danhMuc()
    {
        return $this->belongsTo(DanhMuc::class, 'id_danh_muc', 'id_danh_muc');
    }

    public function thuocTinh()
    {
        return $this->belongsTo(ThuocTinh::class, 'id_thuoc_tinh', 'id_thuoc_tinh');
    }
}
