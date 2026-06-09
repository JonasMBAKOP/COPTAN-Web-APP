<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $fillable = [
        'full_name',
        'short_name',
        'logo',
        'address',
        'postal_box',
        'city',
        'region',
        'email',
        'website',
        'motto',
        'order_type',
        'ministry',
    ];

    // Récupère l'unique enregistrement de paramètres
    public static function instance(): static
    {
        return static::firstOrCreate([], [
            'full_name'  => 'Collège Polyvalent NTANKEU',
            'short_name' => 'COPTAN',
        ]);
    }
}