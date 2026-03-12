<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_id',
        'module_id',
        'title',
        'icon',
        'route',
        'type',
        'sort_order',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active'     => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function parent(): BelongsTo
    {
        return $this->belongsTo(NavItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(NavItem::class, 'parent_id')
            ->orderBy('sort_order');
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
