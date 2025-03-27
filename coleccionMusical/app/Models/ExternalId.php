<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalId extends Model
{
    protected $table = 'external_ids';
    protected $primaryKey = 'id';

    protected $fillable = [
        'supplier', 'value', 'item_id'
    ];

    /**
     * Obtiene el item al que pertenece el externalId
     */

     public function item(): BelongsTo {
         return $this->belongsTo(Item::class);
     }
}
