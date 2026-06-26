<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
        // 'appreciation' stocke le CODE court : CNA, CMA, CA, CBA, CTBA
        'appreciation',
    ];

    protected function casts(): array
    {
        return [
            'coefficient'     => 'decimal:1',
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

    // ── Accesseurs ────────────────────────────────────────────────────────

    /**
     * Renvoie le label complet de l'appréciation en français.
     * Ex: 'CA' → 'Compétences Acquises'
     */
    protected function appreciationLabel(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->appreciation) {
                return null;
            }

            return match ($this->appreciation) {
                'CTBA' => 'Compétences Très Bien Acquises',
                'CBA'  => 'Compétences Bien Acquises',
                'CA'   => 'Compétences Acquises',
                'CMA'  => 'Compétences Moyennement Acquises',
                'CNA'  => 'Compétences Non Acquises',
                default => $this->appreciation,
            };
        });
    }

    /**
     * Renvoie les couleurs CSS associées au code d'appréciation.
     * @return array{bg: string, color: string}
     */
    protected function appreciationColors(): Attribute
    {
        return Attribute::get(function () {
            return AppreciationScale::colorsForCode($this->appreciation ?? '');
        });
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