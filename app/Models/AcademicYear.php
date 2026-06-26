<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = [
        'label',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
            'is_active'  => 'boolean',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────
    public function trimesters()
    {
        return $this->hasMany(Trimester::class)->orderBy('number');
    }

    public function sequences()
    {
        return $this->hasMany(Sequence::class)->orderBy('number');
    }

    public function classGroups()
    {
        return $this->hasMany(ClassGroup::class);
    }

    public function studentEnrollments()
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    // ── Méthodes utilitaires ───────────────────────────────────────────────
    // Récupère l'année scolaire active
    public static function active(): ?static
    {
        return static::where('is_active', true)->first();
    }

    // Active cette année et désactive les autres
    public function activate(): void
    {
        static::query()->update(['is_active' => false]);
        $this->update(['is_active' => true]);
    }

    // Scope : année active
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Vérifie si l'année est clôturée (non modifiable)
    public function isClosed(): bool
    {
        return !$this->is_active && $this->end_date?->isPast();
    }

    /** @return list<array{month: int, year: int, label: string, full_label: string}> */
    public function monthPeriods(): array
    {
        if (! $this->start_date || ! $this->end_date) {
            return [];
        }

        $shortLabels = [
            1 => 'Jan', 2 => 'Fév', 3 => 'Mar', 4 => 'Avr',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juil', 8 => 'Aoû',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Déc',
        ];

        $periods = [];
        $current = $this->start_date->copy()->startOfMonth();
        $end     = $this->end_date->copy()->startOfMonth();

        while ($current <= $end) {
            $periods[] = [
                'month'      => $current->month,
                'year'       => $current->year,
                'label'      => $shortLabels[$current->month],
                'full_label' => $current->locale('fr')->translatedFormat('M Y'),
            ];
            $current->addMonth();
        }

        return $periods;
    }
}
