<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPayment extends Model
{
    protected $fillable = [
        'student_enrollment_id',
        'fee_installment_id',
        'amount_paid',
        'payment_date',
        'payment_method',
        'reference',
        'receipt_number',
        'recorded_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount_paid'  => 'decimal:0',
            'payment_date' => 'date',
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

    // Génère un numéro de reçu unique
    public static function generateReceiptNumber(): string
    {
        $year  = date('Y');
        $count = static::count() + 1;
        return 'RCP-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}