<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Product extends Model
{
    use AsSource, Filterable, Attachable;

    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
        'sku',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $allowedFilters = [
        'name',
        'sku',
        'is_active',
    ];

    protected $allowedSorts = [
        'name',
        'price',
        'quantity',
        'created_at',
        'updated_at',
    ];
}
