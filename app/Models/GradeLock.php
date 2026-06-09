<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeLock extends Model
{
    protected $fillable = [
        'class_group_id',
        'sequence_id',
        'is_locked',
        'locked_by',
        'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'is_locked' => 'boolean',
            'locked_at' => 'datetime',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────
    public function classGroup()
    {
        return $this->belongsTo(ClassGroup::class);
    }

    public function sequence()
    {
        return $this->belongsTo(Sequence::class);
    }

    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }
}