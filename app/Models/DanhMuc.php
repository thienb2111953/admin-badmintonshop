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
}
