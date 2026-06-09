<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trimester extends Model
{
    protected $fillable = [
        'academic_year_id',
        'number',
        'label',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
            'number'     => 'integer',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function sequences()
    {
        return $this->hasMany(Sequence::class)->orderBy('number');
    }

    public function bulletinReports()
    {
        return $this->hasMany(BulletinReport::class);
    }
}