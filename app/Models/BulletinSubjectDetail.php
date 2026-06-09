<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulletinSubjectDetail extends Model
{
    protected $fillable = [
        'bulletin_report_id',
        'class_subject_id',
        'subject_order',
        'coefficient',
        'teacher_name',
        'seq_grade',
        'seq1_grade',
        'seq2_grade',
        'trim1_average',
        'trim2_average',
        'trim3_average',
        'average',
        'total',
        'rank_in_subject',
        'appreciation',
    ];

    protected function casts(): array
    {
        return [
            'coefficient'     => 'integer',
            'seq_grade'       => 'decimal:2',
            'seq1_grade'      => 'decimal:2',
            'seq2_grade'      => 'decimal:2',
            'trim1_average'   => 'decimal:2',
            'trim2_average'   => 'decimal:2',
            'trim3_average'   => 'decimal:2',
            'average'         => 'decimal:2',
            'total'           => 'decimal:2',
            'rank_in_subject' => 'integer',
            'subject_order'   => 'integer',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────
    public function bulletinReport()
    {
        return $this->belongsTo(BulletinReport::class);
    }

    public function classSubject()
    {
        return $this->belongsTo(ClassSubject::class);
    }
}