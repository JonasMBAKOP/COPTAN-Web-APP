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

    public static function ordered()
    {
        return static::orderBy('order_index')->get();
    }

    /** @return array{code: string, label: string, bg: string, color: string}|null */
    public static function uiForGrade(?float $grade): ?array
    {
        if ($grade === null) {
            return null;
        }

        $scale = static::forGrade($grade);
        if (! $scale) {
            return null;
        }

        $colors = static::colorsForCode($scale->code);

        return [
            'code'  => $scale->code,
            'label' => $scale->code,
            'bg'    => $colors['bg'],
            'color' => $colors['color'],
        ];
    }

    /** @return list<array{min: float, max: float, code: string, label: string, bg: string, color: string}> */
    public static function toJsArray(): array
    {
        return static::ordered()
            ->sortByDesc('min_grade')
            ->values()
            ->map(function (self $scale) {
                $colors = static::colorsForCode($scale->code);

                return [
                    'min'   => (float) $scale->min_grade,
                    'max'   => (float) $scale->max_grade,
                    'code'  => $scale->code,
                    'label' => $scale->code,
                    'bg'    => $colors['bg'],
                    'color' => $colors['color'],
                ];
            })
            ->all();
    }

    /** @return array{bg: string, color: string} */
    public static function colorsForCode(string $code): array
    {
        return match ($code) {
            'CTBA'  => ['bg' => '#D1FAE5', 'color' => '#065F46'],
            'CBA'   => ['bg' => '#DBEAFE', 'color' => '#1D4ED8'],
            'CA'    => ['bg' => '#EDE9FE', 'color' => '#6D28D9'],
            'CMA'   => ['bg' => '#FEF3C7', 'color' => '#92400E'],
            'CNA'   => ['bg' => '#FEE2E2', 'color' => '#991B1B'],
            default => ['bg' => '#F3F4F6', 'color' => '#6B7280'],
        };
    }
}