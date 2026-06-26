<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distinction extends Model
{
    protected $fillable = [
        'code',
        'label_fr',
        'label_en',
        'type',
        'order_index',
    ];

    protected function casts(): array
    {
        return [
            'order_index' => 'integer',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────
    public function bulletinReports()
    {
        return $this->hasMany(BulletinReport::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────
    public function scopePositive($query)
    {
        return $query->where('type', 'positive')->orderBy('order_index');
    }

    public function scopeNegative($query)
    {
        return $query->where('type', 'negative')->orderBy('order_index');
    }

    public function getLabelAttribute(): string
    {
        return $this->label_fr ?? '';
    }
}