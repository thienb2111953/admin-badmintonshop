<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaiDat extends Model
{
    protected $table = 'cai_dat';
    protected $primaryKey = 'id_cai_dat';
    public $incrementing = true;

    protected $fillable = [
        'ten_cai_dat',
        'gia_tri',
    ];
}
