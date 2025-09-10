<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuyenACL extends Model
{
    protected $table = 'quyen_acl';
    protected $primaryKey = 'id_quyen_acl';
    public $incrementing = true;

    protected $fillable = [
        'id_quyen',
        'id_path',
        'is_read',
        'is_write',
        'is_update',
        'is_delete',
    ];

    public function quyen_acl(): BelongsTo
    {
        return $this->belongsTo(Quyen::class);
    }
}
