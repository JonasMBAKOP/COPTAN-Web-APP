<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolPhone extends Model
{
    protected $fillable = [
        'number',
        'label',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    // Scope : numéro principal
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}