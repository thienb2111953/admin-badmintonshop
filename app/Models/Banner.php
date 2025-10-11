<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banner';
    protected $primaryKey = 'id_banner';
    public $incrementing = true;

    protected $fillable = [
        'img_url',
        'thu_tu',
        'href',
    ];
}
