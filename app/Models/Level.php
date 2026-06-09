<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $fillable = [
        'section_id',
        'name',
        'order_index',
        'is_exam_class',
    ];

    protected function casts(): array
    {
        return [
            'order_index'   => 'integer',
            'is_exam_class' => 'boolean',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function classGroups()
    {
        return $this->hasMany(ClassGroup::class);
    }

    // ── Méthodes utilitaires ───────────────────────────────────────────────
    // Indique si "Redouble si Échec" s'applique à ce niveau
    public function allowsConditionalRepeat(): bool
    {
        return $this->is_exam_class && $this->name !== '3ème';
    }
}