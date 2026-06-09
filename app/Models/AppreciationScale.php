<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppreciationScale extends Model
{
    protected $fillable = [
        'code',
        'label_fr',
        'label_en',
        'min_grade',
        'max_grade',
        'order_index',
    ];

    protected function casts(): array
    {
        return [
            'min_grade'   => 'decimal:2',
            'max_grade'   => 'decimal:2',
            'order_index' => 'integer',
        ];
    }

    // ── Méthodes utilitaires ───────────────────────────────────────────────
    // Récupère l'appréciation correspondant à une note
    public static function forGrade(float $grade): ?static
    {
        return static::where('min_grade', '<=', $grade)
                     ->where('max_grade', '>=', $grade)
                     ->orderBy('order_index')
                     ->first();
    }
}