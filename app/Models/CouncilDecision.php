<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouncilDecision extends Model
{
    protected $fillable = [
        'label_fr',
        'label_en',
        'exam_classes_only',
        'is_active',
        'order_index',
    ];

    protected function casts(): array
    {
        return [
            'exam_classes_only' => 'boolean',
            'is_active'         => 'boolean',
            'order_index'       => 'integer',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────
    public function bulletinReports()
    {
        return $this->hasMany(BulletinReport::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order_index');
    }

    public function scopeForLevel($query, Level $level)
    {
        return $query->active()->where(fn($q) =>
            $q->where('exam_classes_only', false)
              ->orWhere(fn($q2) =>
                  $q2->where('exam_classes_only', true)
                     ->whereRaw('? = 1 AND ? != "3ème"',
                         [$level->is_exam_class ? 1 : 0, $level->name])
              )
        );
    }

    public function getLabelAttribute(): string
    {
        return $this->label_fr ?? '';
    }
}