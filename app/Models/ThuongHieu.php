<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThuongHieu extends Model
{
    protected $table = 'thuong_hieu';
    protected $primaryKey = 'id_thuong_hieu';
    public $incrementing = true;

    protected $fillable = [
        'ma_thuong_hieu',
        'ten_thuong_hieu',
        'logo_url',
    ];

    public function danhMucs()
    {
        return $this->belongsToMany(
            DanhMuc::class,
            'danh_muc_thuong_hieu',
            'id_thuong_hieu',
            'id_danh_muc'
        )->withPivot('id_danh_muc_thuong_hieu')->withTimestamps();
    }
}
