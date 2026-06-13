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
}
