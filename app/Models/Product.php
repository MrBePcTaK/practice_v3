<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'type',
        'active',
        'category',
        'name',
        'ingredients',
        'weight',
        'price',
        'date',
    ];

    protected $casts = [
        'type'        => 'string',
        'active'      => 'boolean',
        'category'    => 'string',
        'name'        => 'string',
        'ingredients' => 'string',
        'weight'      => 'string',
        'price'       => 'integer',
        'date'        => 'date',
    ];

    public function getMenuToday() {
        return $this->query()
            ->where('date', date('Y-m-d'))
            ->get()->groupBy('category');
    }
}
