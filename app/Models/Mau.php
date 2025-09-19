<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mau extends Model
{
    protected $table = 'mau';
    protected $primaryKey = 'id_mau';
    public $incrementing = true;

    protected $fillable = [
        'ten_mau',
    ];

    public function sanPhamChiTiet()
    {
        return $this->hasMany(SanPhamChiTiet::class, 'id_mau', 'id_mau');
    }
}
