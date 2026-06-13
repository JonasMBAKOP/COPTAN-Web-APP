<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClassGroupRequest;
use App\Http\Requests\UpdateClassGroupRequest;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\ClassGroup;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudentPayment;
use App\Models\Level;
use App\Models\Section;
use App\Models\Staff;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;


class DashboardController extends Controller
{
    // ── REDIRECTION SELON LE RÔLE ─────────────────────────────────────────
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasRole('super-admin'))         return redirect()->route('dashboard.admin');
        if ($user->hasRole('directeur'))           return redirect()->route('dashboard.directeur');
        if ($user->hasRole('fondateur'))           return redirect()->route('dashboard.directeur');
        if ($user->hasRole('censeur'))             return redirect()->route('dashboard.censeur');
        if ($user->hasRole('econome'))             return redirect()->route('dashboard.econome');
        if ($user->hasRole('enseignant'))          return redirect()->route('dashboard.enseignant');
        if ($user->hasRole('surveillant-general')) return redirect()->route('dashboard.surveillant');

        return redirect()->route('dashboard.econome');
    }

    // ── SUPER ADMIN / DIRECTEUR ───────────────────────────────────────────
    public function admin()
    {
        return view('dashboard.admin');
    }

    public function directeur()
    {
        return view('dashboard.directeur');
    }

    // ── CENSEUR ───────────────────────────────────────────────────────────
    public function censeur()
    {
        return view('dashboard.censeur');
    }

    // ── ENSEIGNANT ────────────────────────────────────────────────────────
    public function enseignant()
    {
        return view('dashboard.enseignant');
    }

    // ── SURVEILLANT GÉNÉRAL ───────────────────────────────────────────────
    public function surveillant()
    {
        return view('dashboard.surveillant');
    }

    // ── ÉCONOME ───────────────────────────────────────────────────────────
    // public function econome()
    // {
    //     $activeYear = AcademicYear::active();

    //     // ── Classes de l'année active ──────────────────────────────────
    //     $classes = $activeYear
    //         ? ClassGroup::where('academic_year_id', $activeYear->id)
    //             ->with(['level.section', 'feeStructures.installments'])
    //             ->withCount(['studentEnrollments as enrolled' => fn($q) =>
    //                 $q->where('status', 'active')
    //             ])
    //             ->orderBy('name')
    //             ->get()
    //         : collect();

    //     // ── KPI financiers ─────────────────────────────────────────────
    //     $totalExpected  = 0;
    //     $totalCollected = 0;
    //     $totalStudents  = 0;

    //     foreach ($classes as $class) {
    //         $fee             = $class->feeStructures->first();
    //         $feeTotal        = $fee?->installments->sum('amount') ?? 0;
    //         $totalExpected  += $feeTotal * $class->enrolled;
    //         $totalStudents  += $class->enrolled;
    //         $totalCollected += StudentPayment::whereHas(
    //             'studentEnrollment', fn($q) =>
    //                 $q->where('class_group_id', $class->id)
    //                   ->where('status', 'active')
    //         )->sum('amount_paid');
    //     }

    //     $collectionRate = $totalExpected > 0
    //         ? round(($totalCollected / $totalExpected) * 100)
    //         : 0;

    //     // ── Encaissements du jour et de la semaine ─────────────────────
    //     $todayTotal = StudentPayment::whereDate('payment_date', today())
    //                     ->sum('amount_paid');

    //     $weekTotal = StudentPayment::whereBetween('payment_date', [
    //                     now()->startOfWeek(), now()->endOfWeek(),
    //                  ])->sum('amount_paid');

    //     // ── Paiements récents ──────────────────────────────────────────
    //     $recentPayments = StudentPayment::with([
    //         'studentEnrollment.student',
    //         'studentEnrollment.classGroup',
    //         'feeInstallment',
    //         'recordedBy',
    //     ])->orderByDesc('payment_date')
    //       ->orderByDesc('created_at')
    //       ->take(8)
    //       ->get();

    //     // ── Débiteurs ──────────────────────────────────────────────────
    //     $debtors             = 0;
    //     $outstandingStudents = collect();

    //     if ($activeYear) {
    //         StudentEnrollment::where('academic_year_id', $activeYear->id)
    //             ->where('status', 'active')
    //             ->with(['student', 'classGroup.feeStructures.installments'])
    //             ->chunk(200, function($enrollments) use (
    //                 &$debtors, &$outstandingStudents
    //             ) {
    //                 foreach ($enrollments as $enr) {
    //                     $due  = $enr->classGroup
    //                                 ->feeStructures->first()
    //                                 ?->installments->sum('amount') ?? 0;
    //                     $paid = StudentPayment::where(
    //                                 'student_enrollment_id', $enr->id
    //                             )->sum('amount_paid');

    //                     if ($paid < $due) {
    //                         $debtors++;
    //                         $outstandingStudents->push([
    //                             'enrollment' => $enr,
    //                             'due'        => $due,
    //                             'paid'       => $paid,
    //                             'remaining'  => $due - $paid,
    //                         ]);
    //                     }
    //                 }
    //             });

    //         $outstandingStudents = $outstandingStudents
    //             ->sortByDesc('remaining')
    //             ->take(5)
    //             ->values();
    //     }

    //     // ── Classes sans frais configurés ──────────────────────────────
    //     $classesWithoutFees = $activeYear
    //         ? ClassGroup::where('academic_year_id', $activeYear->id)
    //             ->whereDoesntHave('feeStructures')
    //             ->with('level.section')
    //             ->get()
    //         : collect();

    //     // ── Stats par mode de paiement ─────────────────────────────────
    //     $paymentByMethod = StudentPayment::when($activeYear, fn($q) =>
    //         $q->whereHas('studentEnrollment', fn($q2) =>
    //             $q2->where('academic_year_id', $activeYear->id)
    //         )
    //     )->selectRaw('payment_method, SUM(amount_paid) as total, COUNT(*) as count')
    //      ->groupBy('payment_method')
    //      ->get()
    //      ->keyBy('payment_method');

    //     return view('dashboards.econome', compact(
    //         'activeYear',
    //         'classes',
    //         'totalExpected',
    //         'totalCollected',
    //         'totalStudents',
    //         'collectionRate',
    //         'todayTotal',
    //         'weekTotal',
    //         'recentPayments',
    //         'debtors',
    //         'outstandingStudents',
    //         'classesWithoutFees',
    //         'paymentByMethod'
    //     ));
    // }
    public function econome()
    {
        $activeYear = AcademicYear::active();
        $userId     = Auth::id();

        // ── PAIEMENTS PAR CETTE ÉCONOME UNIQUEMENT ─────────────────────────

        $todayPayments = StudentPayment::where('recorded_by', $userId)
            ->whereDate('payment_date', today())->get();
        $todayAmount   = $todayPayments->sum('amount_paid');
        $todayCount    = $todayPayments->count();

        $weekAmount = StudentPayment::where('recorded_by', $userId)
            ->whereBetween('payment_date', [
                now()->startOfWeek(), now()->endOfWeek()
            ])->sum('amount_paid');

        // Derniers paiements enregistrés par elle
        $recentPayments = StudentPayment::where('recorded_by', $userId)
            ->with([
                'studentEnrollment.student',
                'studentEnrollment.classGroup',
                'feeInstallment',
            ])
            ->orderByDesc('payment_date')
            ->orderByDesc('created_at')
            ->take(5)->get();

        // ── COLLECTE MENSUELLE (par elle, 6 derniers mois) ─────────────────
        $monthlyRaw = StudentPayment::where('recorded_by', $userId)
            ->selectRaw(
                'YEAR(payment_date) as year,
                MONTH(payment_date) as month,
                SUM(amount_paid) as total,
                COUNT(*) as count'
            )
            ->where('payment_date', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get();

        $monthLabels = ['Jan','Fév','Mar','Avr','Mai','Juin',
                        'Juil','Aoû','Sep','Oct','Nov','Déc'];
        $chartData   = [];

        for ($i = 5; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $y     = (int) $date->format('Y');
            $m     = (int) $date->format('m');
            $found = $monthlyRaw->first(
                fn($r) => $r->year == $y && $r->month == $m
            );
            $chartData[] = [
                'label' => $monthLabels[$m - 1],
                'total' => $found ? (float) $found->total : 0,
                'count' => $found ? (int)   $found->count : 0,
            ];
        }

        // ── INSCRIPTIONS ───────────────────────────────────────────────────

        $totalEnrolled = $activeYear
            ? StudentEnrollment::where('academic_year_id', $activeYear->id)
                ->where('status', 'active')->count()
            : 0;

        $newEnrollmentsWeek = $activeYear
            ? StudentEnrollment::where('academic_year_id', $activeYear->id)
                ->whereBetween('created_at', [
                    now()->startOfWeek(), now()->endOfWeek()
                ])->count()
            : 0;

        // Réinscriptions en attente = anciens élèves pas encore inscrits cette année
        $pendingReEnrollments = 0;
        if ($activeYear) {
            $prevYear = AcademicYear::where('is_active', false)
                ->where('id', '<', $activeYear->id)
                ->orderByDesc('id')->first();

            if ($prevYear) {
                $prevIds    = StudentEnrollment::where('academic_year_id', $prevYear->id)
                    ->where('status', 'active')->pluck('student_id');
                $currentIds = StudentEnrollment::where('academic_year_id', $activeYear->id)
                    ->pluck('student_id');
                $pendingReEnrollments = $prevIds->diff($currentIds)->count();
            }
        }

        // Inscriptions récentes
        $recentEnrollments = $activeYear
            ? StudentEnrollment::where('academic_year_id', $activeYear->id)
                ->with(['student', 'classGroup.level.section'])
                ->orderByDesc('created_at')
                ->take(6)->get()
            : collect();

        return view('dashboards.econome', compact(
            'activeYear',
            'todayAmount', 'todayCount', 'weekAmount',
            'recentPayments',
            'chartData',
            'totalEnrolled', 'newEnrollmentsWeek', 'pendingReEnrollments',
            'recentEnrollments'
        ));
    }
}