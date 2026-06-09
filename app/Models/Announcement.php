<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'author_id',
        'title',
        'content',
        'category',
        'target_roles',
        'is_pinned',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'target_roles' => 'array',
            'is_pinned'    => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    // ── Méthodes utilitaires ───────────────────────────────────────────────
    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'pedagogique'   => 'Pédagogique',
            'administratif' => 'Administratif',
            'financier'     => 'Financier',
            'evenement'     => 'Événement',
            default         => 'Général',
        };
    }

    public function isVisibleFor(User $user): bool
    {
        if (empty($this->target_roles)) return true;
        foreach ($user->roles as $role) {
            if (in_array($role->name, $this->target_roles)) return true;
        }
        return false;
    }
}