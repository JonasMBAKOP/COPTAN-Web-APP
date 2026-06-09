<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolAgreement extends Model
{
    protected $fillable = [
        'number',
        'cycle',
        'label',
        'issued_date',
    ];

    protected function casts(): array
    {
        return [
            'issued_date' => 'date',
        ];
    }

    // Labels lisibles pour les cycles
    public function getCycleLabelAttribute(): string
    {
        return match($this->cycle) {
            'premier_cycle' => 'Premier Cycle (6ème – 3ème)',
            'second_cycle'  => 'Second Cycle (2nde – Terminale)',
            default         => 'Autre',
        };
    }
}