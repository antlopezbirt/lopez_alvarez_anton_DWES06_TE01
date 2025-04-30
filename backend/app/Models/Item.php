<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $table = 'items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'title', 'artist', 'format', 'year', 'origyear', 'label', 'rating', 
        'comment', 'buyprice', 'condition', 'sellprice'
    ];

    public function externalIds(): HasMany {

        return $this->hasMany(ExternalId::class);
    }

}
