<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulletinReport extends Model
{
    protected $fillable = [
        'student_enrollment_id',
        'type',
        'sequence_id',
        'trimester_id',
        'academic_year_id',
        'average_general',
        'rank',
        'class_size',
        'class_average',
        'highest_average',
        'lowest_average',
        'justified_absences',
        'unjustified_absences',
        'council_decision_id',
        'distinction_id',
        'general_observation',
        'is_published',
        'generated_at',
        'generated_by',
    ];

    protected function casts(): array
    {
        return [
            'average_general'      => 'decimal:2',
            'class_average'        => 'decimal:2',
            'highest_average'      => 'decimal:2',
            'lowest_average'       => 'decimal:2',
            'justified_absences'   => 'decimal:1',
            'unjustified_absences' => 'decimal:1',
            'rank'                 => 'integer',
            'class_size'           => 'integer',
            'is_published'         => 'boolean',
            'generated_at'         => 'datetime',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────
    public function studentEnrollment()
    {
        return $this->belongsTo(StudentEnrollment::class);
    }

    public function sequence()
    {
        return $this->belongsTo(Sequence::class);
    }

    public function trimester()
    {
        return $this->belongsTo(Trimester::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function councilDecision()
    {
        return $this->belongsTo(CouncilDecision::class);
    }

    public function distinction()
    {
        return $this->belongsTo(Distinction::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function subjectDetails()
    {
        return $this->hasMany(BulletinSubjectDetail::class)
                    ->orderBy('subject_order');
    }
}