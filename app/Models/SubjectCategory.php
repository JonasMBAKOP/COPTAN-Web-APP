<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_fr',
        'name_en',
        'order_index',
    ];

    protected function casts(): array
    {
        return [
            'order_index' => 'integer',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}