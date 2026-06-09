<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffPosition extends Model
{
    protected $fillable = [
        'staff_id',
        'position',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    // ── Méthodes utilitaires ───────────────────────────────────────────────
    public function getPositionLabelAttribute(): string
    {
        return match($this->position) {
            'enseignant'          => 'Enseignant(e)',
            'censeur'             => 'Censeur / Préfet des études',
            'prefet_des_etudes'   => 'Préfet des études',
            'econome'             => 'Économe',
            'surveillant_general' => 'Surveillant(e) Général(e)',
            'directeur'           => 'Directeur / Principal',
            'fondateur'           => 'Fondateur / Fondatrice',
            'secretaire'          => 'Secrétaire',
            default               => 'Autre',
        };
    }
}