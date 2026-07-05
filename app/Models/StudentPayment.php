<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPayment extends Model
{
    protected $fillable = [
        'student_enrollment_id',
        'parent_payment_id',
        'fee_installment_id',
        'amount_paid',
        'payment_date',
        'payment_method',
        'reference',
        'receipt_number',
        'recorded_by',
        'notes',
        'is_bulk',
    ];

    protected function casts(): array
    {
        return [
            'amount_paid'  => 'decimal:0',
            'payment_date' => 'date',
            'is_bulk'      => 'boolean',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────
    public function studentEnrollment()
    {
        return $this->belongsTo(StudentEnrollment::class);
    }

    public function feeInstallment()
    {
        return $this->belongsTo(FeeInstallment::class);
    }

    public function parentPayment()
    {
        return $this->belongsTo(self::class, 'parent_payment_id');
    }

    public function allocations()
    {
        return $this->hasMany(self::class, 'parent_payment_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // ── Méthodes utilitaires ───────────────────────────────────────────────
    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'cash'          => 'Espèces',
            'orange_money'  => 'Orange Money',
            'mtn_momo'      => 'MTN MoMo',
            'bank_transfer' => 'Virement bancaire',
            default         => 'Autre',
        };
    }

    public function scopeVisible($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('parent_payment_id')
              ->orWhere('is_bulk', true);
        });
    }

    public function getDisplayLabelAttribute(): string
    {
        if ($this->is_bulk) {
            return 'Paiement en bloc';
        }

        return $this->feeInstallment?->label ?? '—';
    }

    public function getAllocationSummaryAttribute(): string
    {
        if (! $this->is_bulk) {
            return $this->feeInstallment?->label ?? '—';
        }

        return $this->allocations
            ->loadMissing('feeInstallment')
            ->map(function ($allocation) {
                $label = $allocation->feeInstallment?->label;
                if (! $label) {
                    return null;
                }

                return $label . ' (' . number_format((int) $allocation->amount_paid, 0, ',', ' ') . ' FCFA)';
            })
            ->filter()
            ->implode(', ');
    }

    // Génère un numéro de reçu unique
    public static function generateReceiptNumber(): string
    {
        $year  = date('Y');
        $count = static::count() + 1;
        return 'RCP-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}