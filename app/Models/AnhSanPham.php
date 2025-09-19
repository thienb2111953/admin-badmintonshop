<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnhSanPham extends Model
{
    protected $table = 'anh_san_pham';
    protected $primaryKey = 'id_anh_san_pham';
    public $incrementing = true;

    protected $fillable = [
        'anh_url',
    ];
}
