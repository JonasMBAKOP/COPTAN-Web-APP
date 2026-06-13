<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeeStructureRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\ClassGroup;
use App\Models\FeeInstallment;
use App\Models\FeeStructure;
use App\Models\Section;
use App\Models\StudentEnrollment;
use App\Models\StudentPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    // ── TABLEAU DE BORD FINANCIER ─────────────────────────────────────────
    public function index(Request $request)
    {
        $activeYear     = AcademicYear::active();
        $selectedYearId = $request->input('year_id', $activeYear?->id);
        $selectedYear   = $selectedYearId
            ? AcademicYear::find($selectedYearId)
            : null;

        $years    = AcademicYear::orderByDesc('start_date')->get();
        $sections = Section::orderBy('id')->get();

        // Classes de l'année sélectionnée avec leurs structures de frais
        $classes = collect();
        if ($selectedYear) {
            $classes = ClassGroup::where('academic_year_id', $selectedYear->id)
                ->with([
                    'level.section',
                    'feeStructures.installments',
                    'studentEnrollments' => fn($q) =>
                        $q->where('status', 'active'),
                ])
                ->orderBy('name')
                ->get();
        }

        // Stats globales
        $totalExpected  = 0;
        $totalCollected = 0;
        $totalStudents  = 0;

        foreach ($classes as $class) {
            $feeStructure = $class->feeStructures->first();
            if (!$feeStructure) continue;

            $enrolledCount  = $class->studentEnrollments->count();
            $installTotal   = $feeStructure->installments->sum('amount');
            $totalExpected  += $installTotal * $enrolledCount;
            $totalStudents  += $enrolledCount;

            // Montant collecté pour cette classe
            $collected = StudentPayment::whereHas('studentEnrollment', fn($q) =>
                $q->where('class_group_id', $class->id)
                  ->where('status', 'active')
            )->sum('amount_paid');
            $totalCollected += $collected;
        }

        // Paiements récents
        $recentPayments = StudentPayment::with([
            'studentEnrollment.student',
            'studentEnrollment.classGroup.level.section',
            'feeInstallment.feeStructure',
            'recordedBy',
        ])->orderByDesc('created_at')->take(10)->get();

        $stats = [
            'expected'    => $totalExpected,
            'collected'   => $totalCollected,
            'outstanding' => max(0, $totalExpected - $totalCollected),
            'students'    => $totalStudents,
            'rate'        => $totalExpected > 0
                ? round(($totalCollected / $totalExpected) * 100)
                : 0,
        ];

        return view('finances.index', compact(
            'selectedYear', 'years', 'sections',
            'classes', 'stats', 'recentPayments', 'activeYear'
        ));
    }

    // ── CONFIGURATION DES FRAIS D'UNE CLASSE ─────────────────────────────
    public function configureFees(ClassGroup $classGroup)
    {
        $classGroup->load([
            'level.section', 'academicYear',
            'feeStructures.installments',
        ]);

        $feeStructure = $classGroup->feeStructures->first();

        return view('finances.fees',
            compact('classGroup', 'feeStructure'));
    }

    // ── ENREGISTREMENT DES FRAIS ──────────────────────────────────────────
    public function saveFees(StoreFeeStructureRequest $request,
                             ClassGroup $classGroup)
    {
        if ($classGroup->academicYear->isClosed()) {
            return back()->with('error',
                'Année clôturée — modification impossible.');
        }

        // Créer ou récupérer la structure de frais
        $feeStructure = FeeStructure::firstOrCreate([
            'academic_year_id' => $classGroup->academic_year_id,
            'class_group_id'   => $classGroup->id,
        ], ['total_amount' => 0]);

        // Supprimer les anciennes tranches sans paiements
        $feeStructure->installments()
            ->whereDoesntHave('payments')
            ->delete();

        $total = 0;
        foreach ($request->input('installments', []) as $i => $item) {
            if (empty($item['label']) || !isset($item['amount'])) continue;

            FeeInstallment::updateOrCreate(
                [
                    'fee_structure_id'   => $feeStructure->id,
                    'installment_number' => $i + 1,
                ],
                [
                    'label'          => $item['label'],
                    'amount'         => $item['amount'],
                    'due_date_start' => $item['due_date_start'] ?: null,
                    'due_date_end'   => $item['due_date_end']   ?: null,
                ]
            );
            $total += $item['amount'];
        }

        $feeStructure->update(['total_amount' => $total]);
        AuditLog::log('fees_configured', $feeStructure);

        return redirect()
            ->route('finances.index')
            ->with('success',
                "Frais de {$classGroup->full_name} configurés. "
                . "Total : " . number_format($total) . " FCFA");
    }

    // ── COMPTE FINANCIER D'UN ÉLÈVE ───────────────────────────────────────
    public function studentAccount(StudentEnrollment $enrollment)
    {
        $enrollment->load([
            'student',
            'classGroup.level.section',
            'classGroup.feeStructures.installments.payments' => fn($q) =>
                $q->where('student_enrollment_id', $enrollment->id),
            'academicYear',
        ]);

        $feeStructure = $enrollment->classGroup->feeStructures->first();

        // Calculer le statut de chaque tranche
        $installments = collect();
        if ($feeStructure) {
            foreach ($feeStructure->installments->sortBy('installment_number')
                as $inst) {
                $paid       = $inst->payments->sum('amount_paid');
                $remaining  = max(0, $inst->amount - $paid);
                $status     = $paid <= 0 ? 'unpaid'
                    : ($paid >= $inst->amount ? 'paid' : 'partial');

                $installments->push([
                    'installment' => $inst,
                    'paid'        => $paid,
                    'remaining'   => $remaining,
                    'status'      => $status,
                ]);
            }
        }

        $totalDue       = $feeStructure?->total_amount ?? 0;
        $totalPaid      = StudentPayment::where(
            'student_enrollment_id', $enrollment->id
        )->sum('amount_paid');
        $totalRemaining = max(0, $totalDue - $totalPaid);

        // Historique des paiements
        $payments = StudentPayment::where(
            'student_enrollment_id', $enrollment->id
        )->with(['feeInstallment', 'recordedBy'])
         ->orderByDesc('payment_date')
         ->get();

        return view('finances.student', compact(
            'enrollment', 'feeStructure',
            'installments', 'totalDue', 'totalPaid',
            'totalRemaining', 'payments'
        ));
    }

    // ── ÉLÈVES D'UNE CLASSE — ÉTAT DES PAIEMENTS ─────────────────────────
    public function classStudents(ClassGroup $classGroup)
    {
        $classGroup->load([
            'level.section',
            'academicYear',
            'feeStructures.installments',
        ]);

        $feeStructure = $classGroup->feeStructures->first();

        $enrollments = StudentEnrollment::where('class_group_id', $classGroup->id)
            ->where('status', 'active')
            ->with('student')
            ->orderBy(
                \App\Models\Student::select('last_name')
                    ->whereColumn('students.id', 'student_enrollments.student_id'),
                'asc'
            )
            ->get()
            ->map(function($enrollment) use ($feeStructure) {
                $due  = $feeStructure?->installments->sum('amount') ?? 0;
                $paid = StudentPayment::where(
                    'student_enrollment_id', $enrollment->id
                )->sum('amount_paid');
                $remaining = max(0, $due - $paid);
                $rate = $due > 0 ? round(($paid / $due) * 100) : 0;
                $status = $paid <= 0 ? 'unpaid'
                    : ($paid >= $due   ? 'paid' : 'partial');

                return compact(
                    'enrollment', 'due', 'paid', 'remaining', 'rate', 'status'
                );
            });

        $totalDue       = $enrollments->sum('due');
        $totalPaid      = $enrollments->sum('paid');
        $totalRemaining = $enrollments->sum('remaining');
        $globalRate     = $totalDue > 0
            ? round(($totalPaid / $totalDue) * 100) : 0;

        return view('finances.class-students', compact(
            'classGroup', 'feeStructure', 'enrollments',
            'totalDue', 'totalPaid', 'totalRemaining', 'globalRate'
        ));
    }

    // ── ENREGISTRER UN PAIEMENT ───────────────────────────────────────────
    public function recordPayment(StorePaymentRequest $request,
                                  StudentEnrollment $enrollment)
    {
        $installment = FeeInstallment::find($request->fee_installment_id);

        // Vérifier qu'on ne dépasse pas le montant de la tranche
        $alreadyPaid = StudentPayment::where([
            'student_enrollment_id' => $enrollment->id,
            'fee_installment_id'    => $installment->id,
        ])->sum('amount_paid');

        $remaining = $installment->amount - $alreadyPaid;

        if ($request->amount_paid > $remaining + 1) {
            return back()->with('error',
                "Le montant saisi ({$request->amount_paid} FCFA) dépasse "
                . "le restant dû ({$remaining} FCFA) pour cette tranche.");
        }

        $payment = StudentPayment::create([
            'student_enrollment_id' => $enrollment->id,
            'fee_installment_id'    => $request->fee_installment_id,
            'amount_paid'           => $request->amount_paid,
            'payment_date'          => $request->payment_date,
            'payment_method'        => $request->payment_method,
            'reference'             => $request->reference,
            'receipt_number'        => StudentPayment::generateReceiptNumber(),
            'recorded_by'           => Auth::id(),
            'notes'                 => $request->notes,
        ]);

        AuditLog::log('payment_recorded', $payment);

        return redirect()
            ->route('finances.student', $enrollment)
            ->with('success',
                "Paiement de " . number_format($request->amount_paid)
                . " FCFA enregistré. Reçu : {$payment->receipt_number}");
    }

    // ── LISTE DE TOUS LES PAIEMENTS ───────────────────────────────────────
    public function payments(Request $request)
    {
        $activeYear     = AcademicYear::active();
        $selectedYearId = $request->input('year_id', $activeYear?->id);

        $query = StudentPayment::with([
            'studentEnrollment.student',
            'studentEnrollment.classGroup.level.section',
            'feeInstallment',
            'recordedBy',
        ]);

        if ($selectedYearId) {
            $query->whereHas('studentEnrollment', fn($q) =>
                $q->where('academic_year_id', $selectedYearId)
            );
        }

        if ($request->filled('class_id')) {
            $query->whereHas('studentEnrollment', fn($q) =>
                $q->where('class_group_id', $request->class_id)
            );
        }

        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('receipt_number', 'like', "%{$s}%")
                  ->orWhereHas('studentEnrollment.student', fn($q2) =>
                      $q2->where('first_name', 'like', "%{$s}%")
                         ->orWhere('last_name',  'like', "%{$s}%")
                         ->orWhere('matricule',  'like', "%{$s}%")
                  )
            );
        }

        $payments = $query->orderByDesc('payment_date')
                          ->orderByDesc('created_at') //Ajout
                          ->paginate(20)
                          ->withQueryString();

        $years   = AcademicYear::orderByDesc('start_date')->get();
        $classes = $selectedYearId
            ? ClassGroup::where('academic_year_id', $selectedYearId)
                ->orderBy('name')->get()
            : collect();

        $totalFiltered = $query->sum('amount_paid');

        return view('finances.payments', compact(
            'payments', 'years', 'classes',
            'selectedYearId', 'totalFiltered'
        )); // Retirer 'totalFiltered' plutard
    }

    // // ── REÇU ──────────────────────────────────────────────────────────────
    // public function receipt(StudentPayment $payment)
    // {
    //     $payment->load([
    //         'studentEnrollment.student',
    //         'studentEnrollment.classGroup.level.section',
    //         'studentEnrollment.academicYear',
    //         'feeInstallment.feeStructure',
    //         'recordedBy',
    //     ]);

    //     $school = \App\Models\SchoolSetting::instance();

    //     return view('finances.receipt',
    //         compact('payment', 'school'));
    // }

    // ── REÇU PAIEMENT UNIQUE ──────────────────────────────────────────────
    public function receipt(StudentPayment $payment)
    {
        $payment->load([
            'studentEnrollment.student',
            'studentEnrollment.classGroup.level.section',
            'studentEnrollment.academicYear',
            'studentEnrollment.classGroup.feeStructures.installments',
            'feeInstallment',
            'recordedBy',
        ]);

        $school      = \App\Models\SchoolSetting::instance();
        $phones      = \App\Models\SchoolPhone::orderByDesc('is_primary')->orderBy('id')->get();
        $enrollment  = $payment->studentEnrollment;

        $feeStructure   = $enrollment->classGroup->feeStructures->first();
        $totalDue       = $feeStructure?->installments->sum('amount') ?? 0;
        $totalPaid      = StudentPayment::where(
                            'student_enrollment_id', $enrollment->id
                        )->sum('amount_paid');
        $totalRemaining = max(0, $totalDue - $totalPaid);

        return view('finances.receipt',
            compact('payment', 'school', 'phones',
                    'totalDue', 'totalPaid', 'totalRemaining'));
    }

    // ── REÇU GLOBAL (tous les paiements d'un élève) ───────────────────────
    public function globalReceipt(StudentEnrollment $enrollment)
    {
        $enrollment->load([
            'student',
            'classGroup.level.section',
            'academicYear',
            'classGroup.feeStructures.installments',
        ]);

        // Paiements du plus récent au plus ancien
        $payments = StudentPayment::where('student_enrollment_id', $enrollment->id)
            ->with(['feeInstallment', 'recordedBy'])
            ->orderByDesc('payment_date')
            ->orderByDesc('created_at')
            ->get();

        $feeStructure = $enrollment->classGroup->feeStructures->first();

        // Résumé par tranche
        $installmentSummary = collect();
        if ($feeStructure) {
            foreach ($feeStructure->installments
                        ->sortBy('installment_number') as $inst) {
                $paid = $payments
                    ->where('fee_installment_id', $inst->id)
                    ->sum('amount_paid');

                $installmentSummary->push([
                    'label'     => $inst->label,
                    'amount'    => $inst->amount,
                    'paid'      => $paid,
                    'remaining' => max(0, $inst->amount - $paid),
                    'due_date'  => $inst->due_date_end,
                ]);
            }
        }

        $totalDue       = $feeStructure?->total_amount ?? 0;
        $totalPaid      = $payments->sum('amount_paid');
        $totalRemaining = max(0, $totalDue - $totalPaid);
        $school         = \App\Models\SchoolSetting::instance();
        $phones         = \App\Models\SchoolPhone::orderByDesc('is_primary')->orderBy('id')->get();

        return view('finances.receipt-global', compact(
            'enrollment', 'payments', 'feeStructure',
            'installmentSummary', 'totalDue', 'totalPaid',
            'totalRemaining', 'school', 'phones'
        ));
    }

    // ── IMPRESSION GROUPÉE (2 reçus / page A4 paysage) ───────────────────
    public function batchReceipts(Request $request)
    {
        $ids = array_filter(explode(',', $request->input('ids', '')));

        if (empty($ids)) {
            return back()->with('error', 'Aucun paiement sélectionné.');
        }

        $payments = StudentPayment::whereIn('id', $ids)
            ->with([
                'studentEnrollment.student',
                'studentEnrollment.classGroup.level.section',
                'studentEnrollment.academicYear',
                'studentEnrollment.classGroup.feeStructures.installments',
                'feeInstallment',
                'recordedBy',
            ])
            ->orderByDesc('payment_date')
            ->get();

        $school = \App\Models\SchoolSetting::instance();
        $phones = \App\Models\SchoolPhone::orderByDesc('is_primary')->orderBy('id')->get();

        $receiptsData = $payments->map(function($payment) {
            $enrollment   = $payment->studentEnrollment;
            $feeStructure = $enrollment->classGroup->feeStructures->first();
            $totalDue     = $feeStructure?->installments->sum('amount') ?? 0;
            $totalPaid    = StudentPayment::where(
                                'student_enrollment_id', $enrollment->id
                            )->sum('amount_paid');
            $totalRemaining = max(0, $totalDue - $totalPaid);
            return compact('payment', 'totalDue', 'totalPaid', 'totalRemaining');
        });

        return view('finances.receipt-batch',
            compact('receiptsData', 'school', 'phones'));
    }

    // ── LISTE PAIEMENTS (tri du plus récent au plus ancien) ───────────────
    
    
    // ── LISTE CLASSES POUR CONFIGURATION DES FRAIS ───────────────────────
    public function feesList(Request $request)
    {
        $activeYear     = AcademicYear::active();
        $selectedYearId = $request->input('year_id', $activeYear?->id);
        $selectedYear   = $selectedYearId
            ? AcademicYear::find($selectedYearId)
            : null;

        $years = AcademicYear::orderByDesc('start_date')->get();

        $classes = collect();
        if ($selectedYear) {
            $classes = ClassGroup::where('academic_year_id', $selectedYear->id)
                ->with([
                    'level.section',
                    'feeStructures.installments',
                    'studentEnrollments' => fn($q) =>
                        $q->where('status', 'active'),
                ])
                ->orderBy('name')
                ->get();
        }

        return view('finances.fees-list',
            compact('classes', 'years', 'selectedYear', 'activeYear'));
    }

    // ── RAPPORTS FINANCIERS ───────────────────────────────────────────────
    public function reports(Request $request)
    {
        $activeYear     = AcademicYear::active();
        $selectedYearId = $request->input('year_id', $activeYear?->id);
        $selectedYear   = $selectedYearId
            ? AcademicYear::find($selectedYearId)
            : null;

        $years = AcademicYear::orderByDesc('start_date')->get();

        // ── Stats par classe
        $classeStats = collect();
        if ($selectedYear) {
            $classes = ClassGroup::where('academic_year_id', $selectedYear->id)
                ->with([
                    'level.section',
                    'feeStructures.installments',
                ])
                ->withCount([
                    'studentEnrollments as enrolled_count' => fn($q) =>
                        $q->where('status', 'active'),
                ])
                ->get();

            foreach ($classes as $class) {
                $fee       = $class->feeStructures->first();
                $feeTotal  = $fee?->installments->sum('amount') ?? 0;
                $expected  = $feeTotal * $class->enrolled_count;
                $collected = \App\Models\StudentPayment::whereHas(
                    'studentEnrollment', fn($q) =>
                        $q->where('class_group_id', $class->id)
                        ->where('status', 'active')
                )->sum('amount_paid');

                $classeStats->push([
                    'class'     => $class,
                    'expected'  => $expected,
                    'collected' => $collected,
                    'remaining' => max(0, $expected - $collected),
                    'rate'      => $expected > 0
                        ? round(($collected / $expected) * 100) : 0,
                ]);
            }
        }

        // ── Stats par mode de paiement
        $paymentMethods = \App\Models\StudentPayment::selectRaw(
            'payment_method, SUM(amount_paid) as total, COUNT(*) as count'
        )
        ->when($selectedYear, fn($q) =>
            $q->whereHas('studentEnrollment', fn($q2) =>
                $q2->where('academic_year_id', $selectedYear->id)
            )
        )
        ->groupBy('payment_method')
        ->get();

        // ── Stats par tranche
        $installmentStats = FeeInstallment::selectRaw(
            'fee_installments.label,
            fee_installments.amount,
            SUM(student_payments.amount_paid) as collected,
            COUNT(DISTINCT student_payments.student_enrollment_id) as payers'
        )
        ->leftJoin('student_payments',
            'student_payments.fee_installment_id', '=', 'fee_installments.id')
        ->leftJoin('fee_structures',
            'fee_structures.id', '=', 'fee_installments.fee_structure_id')
        ->when($selectedYear, fn($q) =>
            $q->where('fee_structures.academic_year_id', $selectedYear->id)
        )
        ->groupBy('fee_installments.id',
                'fee_installments.label',
                'fee_installments.amount')
        ->orderBy('fee_installments.installment_number')
        ->get();

        // ── Élèves avec solde impayé
        $debtors = \App\Models\StudentEnrollment::where('status', 'active')
            ->when($selectedYear, fn($q) =>
                $q->where('academic_year_id', $selectedYear->id)
            )
            ->with([
                'student',
                'classGroup.level.section',
                'classGroup.feeStructures.installments',
            ])
            ->get()
            ->map(function($e) {
                $fee      = $e->classGroup->feeStructures->first();
                $due      = $fee?->installments->sum('amount') ?? 0;
                $paid     = \App\Models\StudentPayment::where(
                    'student_enrollment_id', $e->id
                )->sum('amount_paid');
                $remaining = max(0, $due - $paid);
                return ['enrollment' => $e, 'due' => $due,
                        'paid' => $paid, 'remaining' => $remaining];
            })
            ->filter(fn($e) => $e['remaining'] > 0)
            ->sortByDesc('remaining')
            ->values();

        // ── Totaux globaux
        $globalStats = [
            'expected'  => $classeStats->sum('expected'),
            'collected' => $classeStats->sum('collected'),
            'remaining' => $classeStats->sum('remaining'),
            'rate'      => $classeStats->sum('expected') > 0
                ? round(($classeStats->sum('collected')
                    / $classeStats->sum('expected')) * 100)
                : 0,
            'debtors'   => $debtors->count(),
        ];

        return view('finances.reports', compact(
            'selectedYear', 'years', 'classeStats',
            'paymentMethods', 'installmentStats',
            'debtors', 'globalStats'
        ));
    }

    // ── TABLEAU DE BORD DE GESTION GLOBALE ───────────────────────────────
    public function global(Request $request)
    {
        $activeYear     = AcademicYear::active();
        $selectedYearId = $request->input('year_id', $activeYear?->id);
        $selectedYear   = $selectedYearId
            ? AcademicYear::find($selectedYearId)
            : null;

        $years = AcademicYear::orderByDesc('start_date')->get();

        // ── Effectif total des élèves actifs pour l'année sélectionnée
        $totalEnrolled = 0;
        if ($selectedYear) {
            $totalEnrolled = StudentEnrollment::where('academic_year_id', $selectedYear->id)
                ->where('status', 'active')
                ->count();
        }

        // ── Stats par classe
        $classeStats = collect();
        if ($selectedYear) {
            $classes = ClassGroup::where('academic_year_id', $selectedYear->id)
                ->with([
                    'level.section',
                    'feeStructures.installments',
                ])
                ->withCount([
                    'studentEnrollments as enrolled_count' => fn($q) =>
                        $q->where('status', 'active'),
                ])
                ->get();

            foreach ($classes as $class) {
                $fee       = $class->feeStructures->first();
                $feeTotal  = $fee?->installments->sum('amount') ?? 0;
                $expected  = $feeTotal * $class->enrolled_count;
                $collected = \App\Models\StudentPayment::whereHas(
                    'studentEnrollment', fn($q) =>
                        $q->where('class_group_id', $class->id)
                        ->where('status', 'active')
                )->sum('amount_paid');

                $classeStats->push([
                    'class'     => $class,
                    'expected'  => $expected,
                    'collected' => $collected,
                    'remaining' => max(0, $expected - $collected),
                    'rate'      => $expected > 0
                        ? round(($collected / $expected) * 100) : 0,
                ]);
            }
        }

        // ── Stats par mode de paiement
        $paymentMethods = \App\Models\StudentPayment::selectRaw(
            'payment_method, SUM(amount_paid) as total, COUNT(*) as count'
        )
        ->when($selectedYear, fn($q) =>
            $q->whereHas('studentEnrollment', fn($q2) =>
                $q2->where('academic_year_id', $selectedYear->id)
            )
        )
        ->groupBy('payment_method')
        ->get();

        // ── Stats par tranche
        $installmentStatsRaw = FeeInstallment::selectRaw(
            'fee_installments.id,
            fee_installments.label,
            fee_installments.installment_number,
            fee_installments.amount,
            fee_installments.due_date_end,
            SUM(student_payments.amount_paid) as collected,
            COUNT(DISTINCT student_payments.student_enrollment_id) as payers'
        )
        ->leftJoin('student_payments',
            'student_payments.fee_installment_id', '=', 'fee_installments.id')
        ->leftJoin('fee_structures',
            'fee_structures.id', '=', 'fee_installments.fee_structure_id')
        ->when($selectedYear, fn($q) =>
            $q->where('fee_structures.academic_year_id', $selectedYear->id)
        )
        ->groupBy('fee_installments.id',
                'fee_installments.label',
                'fee_installments.installment_number',
                'fee_installments.amount',
                'fee_installments.due_date_end')
        ->orderBy('fee_installments.installment_number')
        ->get();

        $installmentStats = $installmentStatsRaw->map(function($is) use ($totalEnrolled) {
            $rate = $totalEnrolled > 0 ? round(($is->payers / $totalEnrolled) * 100) : 0;
            
            $dueDate = '';
            if ($is->due_date_end) {
                try {
                    $dueDate = \Carbon\Carbon::parse($is->due_date_end)->translatedFormat('d F');
                } catch (\Exception $e) {
                    $dueDate = $is->due_date_end;
                }
            } else {
                $dueDate = match($is->installment_number) {
                    1 => '15 Septembre',
                    2 => '15 Décembre',
                    3 => '15 Mars',
                    default => 'Non définie',
                };
            }
            
            return (object) [
                'id' => $is->id,
                'label' => $is->label,
                'installment_number' => $is->installment_number,
                'amount' => $is->amount,
                'collected' => $is->collected ?? 0,
                'payers' => $is->payers ?? 0,
                'rate' => $rate,
                'due_date' => $dueDate,
            ];
        });

        // ── Élèves avec solde impayé (Débiteurs)
        $debtors = \App\Models\StudentEnrollment::where('status', 'active')
            ->when($selectedYear, fn($q) =>
                $q->where('academic_year_id', $selectedYear->id)
            )
            ->with([
                'student',
                'classGroup.level.section',
                'classGroup.feeStructures.installments',
            ])
            ->get()
            ->map(function($e) {
                $fee      = $e->classGroup->feeStructures->first();
                $due      = $fee?->installments->sum('amount') ?? 0;
                $paid     = \App\Models\StudentPayment::where(
                    'student_enrollment_id', $e->id
                )->sum('amount_paid');
                $remaining = max(0, $due - $paid);
                return ['enrollment' => $e, 'due' => $due,
                        'paid' => $paid, 'remaining' => $remaining];
            })
            ->filter(fn($e) => $e['remaining'] > 0)
            ->sortByDesc('remaining')
            ->values();

        // ── Totaux globaux
        $globalStats = [
            'expected'  => $classeStats->sum('expected'),
            'collected' => $classeStats->sum('collected'),
            'remaining' => $classeStats->sum('remaining'),
            'rate'      => $classeStats->sum('expected') > 0
                ? round(($classeStats->sum('collected')
                    / $classeStats->sum('expected')) * 100)
                : 0,
            'debtors'   => $debtors->count(),
        ];

        // ── Calcul du taux d'élèves à jour
        $debtorsCount = $debtors->count();
        $paidInFullCount = max(0, $totalEnrolled - $debtorsCount);
        $paidInFullRate = $totalEnrolled > 0 ? round(($paidInFullCount / $totalEnrolled) * 100) : 0;

        // ── Stats par section
        $sectionStats = collect();
        $sections = Section::all();
        foreach ($sections as $sec) {
            $sectionStats->put($sec->id, [
                'section' => $sec,
                'expected' => 0,
                'collected' => 0,
            ]);
        }
        foreach ($classeStats as $row) {
            $secId = $row['class']->level->section_id;
            if ($sectionStats->has($secId)) {
                $current = $sectionStats->get($secId);
                $current['expected'] += $row['expected'];
                $current['collected'] += $row['collected'];
                $sectionStats->put($secId, $current);
            }
        }
        $sectionStats = $sectionStats->map(function($item) {
            $expected = $item['expected'];
            $collected = $item['collected'];
            $remaining = max(0, $expected - $collected);
            $rate = $expected > 0 ? round(($collected / $expected) * 100) : 0;
            return (object) array_merge($item, [
                'remaining' => $remaining,
                'rate' => $rate,
            ]);
        });

        // ── Encaissements du jour
        $todayPaymentsCount = StudentPayment::whereDate('payment_date', today())->count();
        $todayPaymentsAmount = StudentPayment::whereDate('payment_date', today())->sum('amount_paid');
        $lastPaymentTime = StudentPayment::orderByDesc('created_at')->first()?->created_at?->format('H:i') ?? '--:--';

        // ── Paiements récents
        $recentPayments = StudentPayment::with([
            'studentEnrollment.student',
            'studentEnrollment.classGroup.level.section',
            'feeInstallment',
        ])
        ->when($selectedYear, fn($q) =>
            $q->whereHas('studentEnrollment', fn($q2) =>
                $q2->where('academic_year_id', $selectedYear->id)
            )
        )
        ->orderByDesc('created_at')
        ->take(5)
        ->get();

        // ── Évolution mensuelle (Septembre à Juin)
        $monthlyData = collect();
        $monthsOrder = [
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Déc',
            1 => 'Jan', 2 => 'Fév', 3 => 'Mar', 4 => 'Avr',
            5 => 'Mai', 6 => 'Juin'
        ];
        
        $rawMonthly = StudentPayment::selectRaw('MONTH(payment_date) as month, SUM(amount_paid) as total')
            ->when($selectedYear, fn($q) =>
                $q->whereHas('studentEnrollment', fn($q2) =>
                    $q2->where('academic_year_id', $selectedYear->id)
                )
            )
            ->groupBy('month')
            ->get()
            ->keyBy('month');
            
        foreach ($monthsOrder as $mNum => $mLabel) {
            $monthlyData->push((object) [
                'label' => $mLabel,
                'total' => $rawMonthly->has($mNum) ? (float) $rawMonthly->get($mNum)->total : 0,
            ]);
        }

        return view('finances.global', compact(
            'selectedYear', 'years', 'classeStats', 'totalEnrolled',
            'paymentMethods', 'installmentStats', 'debtors', 'globalStats',
            'paidInFullRate', 'sectionStats', 'todayPaymentsCount',
            'todayPaymentsAmount', 'lastPaymentTime', 'recentPayments',
            'monthlyData'
        ));
    }
}