<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'language',
    ];

    // ── Relations ──────────────────────────────────────────────────────────
    public function levels()
    {
        return $this->hasMany(Level::class)->orderBy('order_index');
    }

    // ── Méthodes utilitaires ───────────────────────────────────────────────
    public function isAnglophone(): bool
    {
        return $this->language === 'en';
    }
}