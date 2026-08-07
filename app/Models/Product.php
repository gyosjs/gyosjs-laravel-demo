<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    public const CATEGORIES = ['Audio', 'Desk', 'Lighting', 'Travel'];

    public const STATUSES = ['active', 'draft', 'archived'];

    protected $fillable = [
        'sku',
        'name',
        'category',
        'status',
        'price',
        'stock',
        'description',
        'image_tone',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(DemoWorkspace::class, 'workspace_id');
    }

    public function scopeForWorkspace(Builder $query, DemoWorkspace $workspace): Builder
    {
        return $query->where('workspace_id', $workspace->getKey());
    }
}
