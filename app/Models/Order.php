<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'cost',
        'date',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'cost' => 'integer',
        'date' => 'date',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'orders_products')->withPivot('count');
    }
}
