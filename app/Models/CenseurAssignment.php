<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CenseurAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'section_id',
        'cycle',
    ];

    // ── Relations ──────────────────────────────────────────────────────────
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    // ── Méthodes utilitaires ───────────────────────────────────────────────
    public function getCycleLabelAttribute(): string
    {
        return match($this->cycle) {
            '1er'   => '1er Cycle',
            '2nd'   => '2nd Cycle',
            default => $this->cycle,
        };
    }
}
