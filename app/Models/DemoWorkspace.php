<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DemoWorkspace extends Model
{
    use HasUuids;

    protected $fillable = ['last_seen_at'];

    protected function casts(): array
    {
        return ['last_seen_at' => 'datetime'];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'workspace_id');
    }
}
