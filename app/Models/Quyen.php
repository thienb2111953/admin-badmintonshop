<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quyen extends Model
{
    protected $table = 'quyen';
    protected $primaryKey = 'id_quyen';
    public $incrementing = true;

    protected $fillable = [
        'ten_quyen',
    ];

}
