<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeInstallment extends Model
{
    protected $fillable = [
        'fee_structure_id',
        'installment_number',
        'label',
        'amount',
        'due_date_start',
        'due_date_end',
    ];

    protected function casts(): array
    {
        return [
            'installment_number' => 'integer',
            'amount'             => 'decimal:0',
            'due_date_start'     => 'date',
            'due_date_end'       => 'date',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────
    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function payments()
    {
        return $this->hasMany(StudentPayment::class);
    }

    // ── Méthodes utilitaires ───────────────────────────────────────────────
    public function getTotalPaidAttribute(): float
    {
        return $this->payments()->sum('amount_paid');
    }
}