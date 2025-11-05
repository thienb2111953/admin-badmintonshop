<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GioHang extends Model
{
    protected $table = 'gio_hang';
    protected $primaryKey = 'id_gio_hang';
    public $incrementing = true;

    protected $fillable = ['id_nguoi_dung'];
}
