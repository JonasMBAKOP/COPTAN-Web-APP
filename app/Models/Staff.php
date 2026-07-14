<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory, SoftDeletes;

    public const POSITIONS = [
        'enseignant',
        'censeur',
        'prefet_des_etudes',
        'econome',
        'surveillant_general',
        'directeur',
        'fondateur',
        'secretaire',
        'autre',
    ];

// Début

    public const DIPLOMAS = [
        'BEPC', 'BAC', 'Licence', 'Master', 'Doctorat', 'Autre',
    ];

    public const CONTRACT_TYPES = [
        'permanent', 'vacataire', 'stagiaire',
    ];

// Fin

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'phone',
        'email',
        'photo',
        'diploma',
        'start_date',
        'contract_type',
        'monthly_salary',
        'hourly_rate',
        'period_rate',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'start_date'    => 'date',
            'is_active'     => 'boolean',
            'monthly_salary'=> 'integer',
            'hourly_rate'   => 'integer',
            'period_rate'   => 'integer',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function positions()
    {
        return $this->hasMany(StaffPosition::class);
    }

    public function teacherAssignments()
    {
        return $this->hasMany(TeacherAssignment::class);
    }

    public function titularClasses()
    {
        return $this->hasMany(ClassGroup::class, 'titular_staff_id');
    }

    // ── Méthodes utilitaires ───────────────────────────────────────────────
    // Nom complet
    public function getFullNameAttribute(): string
    {
        return "{$this->last_name} {$this->first_name}";
    }

    // Nom complet avec civilité
    public function getHonorificFullNameAttribute(): string
    {
        $gender = strtolower((string) $this->gender);
        $prefix = in_array($gender, ['female', 'femme', 'f'], true) ? 'Mme' : 'M.';

        return $prefix . ' ' . mb_strtoupper($this->full_name);
    }

    // Poste principal
    public function getPrimaryPositionAttribute(): ?StaffPosition
    {
        return $this->positions()->where('is_primary', true)->first();
    }

    // URL de la photo
    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : asset('images/default-avatar.png');
    }

// Début

    public function getContractLabelAttribute(): string
    {
        return match ($this->contract_type) {
            'permanent' => 'Permanent',
            'vacataire' => 'Vacataire',
            'stagiaire' => 'Stagiaire',
            default     => $this->contract_type,
        };
    }

    public function getDiplomaLabelAttribute(): ?string
    {
        return $this->diploma;
    }

    public function getSalaryDisplayAttribute(): string
    {
        if ($this->contract_type === 'permanent') {
            return $this->monthly_salary
                ? number_format($this->monthly_salary) . ' FCFA / mois'
                : 'À renseigner';
        }

        if ($this->contract_type === 'vacataire') {
            return $this->hourly_rate
                ? number_format($this->hourly_rate) . ' FCFA / h'
                : 'À renseigner';
        }

        $parts = [];
        if ($this->hourly_rate) {
            $parts[] = number_format($this->hourly_rate) . ' FCFA / h';
        }
        if ($this->period_rate) {
            $parts[] = number_format($this->period_rate) . ' FCFA / période';
        }

        return $parts ? implode(' / ', $parts) : 'À renseigner';
    }

    public function isTeacher(): bool
    {
        return $this->positions()
            ->where('position', 'enseignant')
            ->exists();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTeachers($query)
    {
        return $query->active()->whereHas('positions', fn ($q) =>
            $q->where('position', 'enseignant')
        );
    }

    public function scopeWithPosition($query, string $position)
    {
        return $query->whereHas('positions', fn ($q) =>
            $q->where('position', $position)
        );
    }

    public static function positionLabels(): array
    {
        return [
            'enseignant'          => 'Enseignant(e)',
            'censeur'             => 'Préfet des études / Dean',
            'prefet_des_etudes'   => 'Préfet des études / Dean',
            'econome'             => 'Économe',
            'surveillant_general' => 'Surveillant(e) Général(e)',
            'directeur'           => 'Directeur / Principal',
            'fondateur'           => 'Fondateur / Fondatrice',
            'secretaire'          => 'Secrétaire',
            'autre'               => 'Autre',
        ];
    }

    public static function contractLabels(): array
    {
        return [
            'permanent' => 'Permanent',
            'vacataire' => 'Vacataire',
            'stagiaire' => 'Stagiaire',
        ];
    }

// Fin
}