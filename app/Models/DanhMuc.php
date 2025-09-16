<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhMuc extends Model
{
    protected $table = 'danh_muc';
    protected $primaryKey = 'id_danh_muc';
    public $incrementing = true;

    protected $fillable = [
        'ten_danh_muc',
        'slug',
    ];

    // Quan hệ nhiều-nhiều với ThuocTinh qua bảng pivot
    public function thuocTinhs()
    {
        return $this->belongsToMany(
            ThuocTinh::class,
            'danh_muc_thuoc_tinh',
            'id_danh_muc',
            'id_thuoc_tinh'
        )->withPivot('id_danh_muc_thuoc_tinh')
            ->withTimestamps();
    }
}
