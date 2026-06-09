<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    protected $fillable = [
        'academic_year_id',
        'class_group_id',
        'total_amount',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:0',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function classGroup()
    {
        return $this->belongsTo(ClassGroup::class);
    }

    public function installments()
    {
        return $this->hasMany(FeeInstallment::class)
                    ->orderBy('installment_number');
    }
}