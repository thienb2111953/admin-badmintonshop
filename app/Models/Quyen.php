<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quyen extends Model
{
    protected $table = 'quyen';
    protected $primaryKey = 'id_quyen';
    public $incrementing = true;

    protected $fillable = [
        'ten_quyen',
    ];

    public function quyen(): HasMany
    {
        return $this->hasMany(Quyen::class);
    }

    public function nguoi_dung(): BelongsTo
    {
        return $this->BelongsTo(User::class);
    }
}
