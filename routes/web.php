<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SchoolSettingController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\ClassManagementController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentDocumentController;
use App\Http\Controllers\FinanceController;


// ── PAGE D'ACCUEIL → Redirection vers login ──────────────────────────────────
Route::get('/', function () {
    return redirect()->route('login');
});

// ── ROUTES D'AUTHENTIFICATION (Breeze) ───────────────────────────────────────
require __DIR__.'/auth.php';

// ── SUPER ADMIN ───────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:super-admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboards.admin');
        })->name('dashboard');
    });

// ── DIRECTEUR ─────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:directeur,super-admin'])
    ->prefix('directeur')
    ->name('directeur.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboards.directeur');
        })->name('dashboard');
    });

// ── CENSEUR ───────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:censeur,super-admin'])
    ->prefix('censeur')
    ->name('censeur.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboards.censeur');
        })->name('dashboard');
    });

// ── ÉCONOME ───────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:econome,super-admin'])
    ->prefix('econome')
    ->name('econome.')
    ->group(function () {
        // Route::get('/dashboard', function () {
        //     return view('dashboards.econome');
        // })->name('dashboard');
        Route::get('/dashboard',
            [\App\Http\Controllers\DashboardController::class, 'econome'])
            ->name('dashboard');
    });

// ── ENSEIGNANT ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:enseignant,super-admin'])
    ->prefix('enseignant')
    ->name('enseignant.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboards.enseignant');
        })->name('dashboard');
    });

// ── SURVEILLANT GÉNÉRAL ───────────────────────────────────────────────────────
Route::middleware(['auth', 'role:surveillant-general,super-admin'])
    ->prefix('surveillant')
    ->name('surveillant.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboards.surveillant');
        })->name('dashboard');
    });

// ── GESTION DES UTILISATEURS ──────────────────────────────────────────────────
Route::middleware(['auth', 'permission:manage-users'])
    ->prefix('users')
    ->name('users.')
    ->group(function () {
        Route::get('/',              [UserController::class, 'index'])
             ->name('index');
        Route::get('/create',        [UserController::class, 'create'])
             ->name('create');
        Route::post('/',             [UserController::class, 'store'])
             ->name('store');
        Route::get('/{user}/edit',   [UserController::class, 'edit'])
             ->name('edit');
        Route::put('/{user}',        [UserController::class, 'update'])
             ->name('update');
        Route::delete('/{user}',     [UserController::class, 'destroy'])
             ->name('destroy');
        Route::patch('/{user}/toggle-active',
                                     [UserController::class, 'toggleActive'])
             ->name('toggle-active');
        Route::post('/{user}/reset-password',
                                     [UserController::class, 'resetPassword'])
             ->name('reset-password');
    });


// ── PARAMÈTRES DE L'ÉTABLISSEMENT ────────────────────────────────────────────
Route::middleware(['auth', 'permission:manage-settings'])
    ->prefix('settings')
    ->name('settings.')
    ->group(function () {
        Route::get('/',   [SchoolSettingController::class, 'index'])
             ->name('index');
        Route::put('/',   [SchoolSettingController::class, 'update'])
             ->name('update');

        // Logo
        Route::post('/logo',   [SchoolSettingController::class, 'updateLogo'])
             ->name('logo.update');
        Route::delete('/logo', [SchoolSettingController::class, 'deleteLogo'])
             ->name('logo.delete');

        // Téléphones
        Route::post('/phones',
            [SchoolSettingController::class, 'storePhone'])
            ->name('phones.store');
        Route::put('/phones/{phone}',
            [SchoolSettingController::class, 'updatePhone'])
            ->name('phones.update');
        Route::delete('/phones/{phone}',
            [SchoolSettingController::class, 'destroyPhone'])
            ->name('phones.destroy');
        Route::patch('/phones/{phone}/primary',
            [SchoolSettingController::class, 'setPrimaryPhone'])
            ->name('phones.primary');

        // Agréments
        Route::post('/agreements',
            [SchoolSettingController::class, 'storeAgreement'])
            ->name('agreements.store');
        Route::put('/agreements/{agreement}',
            [SchoolSettingController::class, 'updateAgreement'])
            ->name('agreements.update');
        Route::delete('/agreements/{agreement}',
            [SchoolSettingController::class, 'destroyAgreement'])
            ->name('agreements.destroy');
    });

// ── ANNÉES SCOLAIRES ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:manage-academic-years'])
    ->prefix('academic-years')
    ->name('academic-years.')
    ->group(function () {
        // Routes fixes
        Route::get('/',    [AcademicYearController::class, 'index'])
             ->name('index');
        Route::get('/create', [AcademicYearController::class, 'create'])
             ->name('create');
        Route::post('/',   [AcademicYearController::class, 'store'])
             ->name('store');

        // Sous-ressources AVANT le wildcard
        Route::patch('/sequences/{sequence}/toggle-lock',
            [AcademicYearController::class, 'toggleSequenceLock'])
            ->name('sequences.toggle-lock');
        Route::delete('/{academicYear}',
            [AcademicYearController::class, 'destroy'])
            ->name('destroy');

        // Wildcard EN DERNIER
        Route::get('/{academicYear}', [AcademicYearController::class, 'show'])
             ->name('show');
        Route::get('/{academicYear}/edit',   [AcademicYearController::class, 'edit'])
             ->name('edit');
        Route::put('/{academicYear}',        [AcademicYearController::class, 'updateAll'])
             ->name('update-all');
        // Route::put('/{academicYear}', [AcademicYearController::class, 'update'])
        //      ->name('update');
        Route::patch('/{academicYear}/activate',
             [AcademicYearController::class, 'activate'])
             ->name('activate');
        Route::patch('/{academicYear}/close',
             [AcademicYearController::class, 'close'])
             ->name('close');
        // // Trimestres
        // Route::put('/trimesters/{trimester}',
        //      [AcademicYearController::class, 'updateTrimester'])
        //      ->name('trimesters.update');
        // // Séquences
        // Route::put('/sequences/{sequence}',
        //      [AcademicYearController::class, 'updateSequence'])
        //      ->name('sequences.update');
        // Route::patch('/sequences/{sequence}/toggle-lock',
        //      [AcademicYearController::class, 'toggleSequenceLock'])
        //      ->name('sequences.toggle-lock');
    });

// ── CLASSES & STRUCTURE ──────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:view-classes'])
    ->prefix('classes')
    ->name('classes.')
    ->group(function () {
        Route::get('/', [ClassManagementController::class, 'index'])
             ->name('index');

        Route::middleware('permission:manage-classes')->group(function () {
            // Niveaux & Sections (avant wildcards)
            Route::post('/sections/{section}/levels',
                [ClassManagementController::class, 'storeLevel'])
                ->name('levels.store');
            Route::put('/levels/{level}',
                [ClassManagementController::class, 'updateLevel'])
                ->name('levels.update');
            Route::delete('/levels/{level}',
                [ClassManagementController::class, 'destroyLevel'])
                ->name('levels.destroy');
            Route::put('/sections/{section}',
                [ClassManagementController::class, 'updateSection'])
                ->name('sections.update');

            // Classes CRUD
            Route::get('/create',
                [ClassManagementController::class, 'create'])
                ->name('create');
            Route::post('/',
                [ClassManagementController::class, 'store'])
                ->name('store');
            Route::get('/{classGroup}/edit',
                [ClassManagementController::class, 'edit'])
                ->name('edit');
            Route::put('/{classGroup}',
                [ClassManagementController::class, 'update'])
                ->name('update');
            Route::delete('/{classGroup}',
                [ClassManagementController::class, 'destroy'])
                ->name('destroy');
        });

        // Wildcard en dernier
        Route::get('/{classGroup}',
            [ClassManagementController::class, 'show'])
            ->name('show');
    });

// // ── MATIÈRES & CATÉGORIES ─────────────────────────────────────────────────────
// Route::middleware(['auth', 'permission:view-subjects'])
//     ->prefix('subjects')
//     ->name('subjects.')
//     ->group(function () {
//         Route::get('/', [SubjectManagementController::class, 'index'])->name('index');

//         Route::middleware('permission:manage-subjects')->group(function () {
//             // Catégories
//             Route::post('/categories',
//                 [SubjectManagementController::class, 'storeCategory'])->name('categories.store');
//             Route::put('/categories/{category}',
//                 [SubjectManagementController::class, 'updateCategory'])->name('categories.update');
//             Route::delete('/categories/{category}',
//                 [SubjectManagementController::class, 'destroyCategory'])->name('categories.destroy');

//             // Matières (catalogue) — sous-ressources avant wildcards
//             Route::post('/classes/{classGroup}/assign',
//                 [SubjectManagementController::class, 'assignToClass'])->name('classes.assign');
//             Route::put('/assignments/{classSubject}',
//                 [SubjectManagementController::class, 'updateClassAssignment'])->name('assignments.update');
//             Route::delete('/assignments/{classSubject}',
//                 [SubjectManagementController::class, 'removeClassAssignment'])->name('assignments.destroy');

//             // Matières catalogue CRUD (wildcards en dernier)
//             Route::post('/', [SubjectManagementController::class, 'storeSubject'])->name('store');
//             Route::put('/{subject}',
//                 [SubjectManagementController::class, 'updateSubject'])->name('update');
//             Route::delete('/{subject}',
//                 [SubjectManagementController::class, 'destroySubject'])->name('destroy');
//         });
//     });


// ── MATIÈRES ─────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:view-subjects'])
    ->prefix('subjects')
    ->name('subjects.')
    ->group(function () {
        Route::get('/', [SubjectController::class, 'index'])
             ->name('index');

        Route::middleware('permission:manage-subjects')->group(function () {
            // Catégories (avant wildcards)
            Route::post('/categories',
                [SubjectController::class, 'storeCategory'])
                ->name('categories.store');
            Route::put('/categories/{category}',
                [SubjectController::class, 'updateCategory'])
                ->name('categories.update');
            Route::delete('/categories/{category}',
                [SubjectController::class, 'destroyCategory'])
                ->name('categories.destroy');

            // Attribution aux classes
            Route::get('/assign/{classGroup}',
                [SubjectController::class, 'assign'])
                ->name('assign');
            Route::post('/assign/{classGroup}',
                [SubjectController::class, 'saveAssignment'])
                ->name('save-assignment');
            Route::post('/assign/{classGroup}/copy-from',
                [SubjectController::class, 'copyFromClass'])
                ->name('copy-from-class');

            // CRUD matières
            Route::get('/create',
                [SubjectController::class, 'create'])->name('create');
            Route::post('/',
                [SubjectController::class, 'store'])->name('store');
            Route::get('/{subject}/edit',
                [SubjectController::class, 'edit'])->name('edit');
            Route::put('/{subject}',
                [SubjectController::class, 'update'])->name('update');
            Route::delete('/{subject}',
                [SubjectController::class, 'destroy'])->name('destroy');
        });
    });

// ── PERSONNEL (ENSEIGNANTS & STAFF) ──────────────────────────────────────────
Route::middleware(['auth', 'permission:view-staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('index');

        Route::middleware('permission:manage-staff')->group(function () {
            // Routes spécifiques AVANT les wildcards
            Route::get('/create', [StaffController::class, 'create'])->name('create');
            Route::post('/', [StaffController::class, 'store'])->name('store');
            Route::get('/{staff}/edit', [StaffController::class, 'edit'])->name('edit');
            Route::put('/{staff}', [StaffController::class, 'update'])->name('update');
            Route::delete('/{staff}/photo', [StaffController::class, 'deletePhoto'])->name('photo.delete');
            Route::delete('/{staff}', [StaffController::class, 'destroy'])->name('destroy');
            Route::patch('/{staff}/toggle',
                [StaffController::class, 'toggleActive'])->name('toggle');
        });

        Route::get('/{staff}', [StaffController::class, 'show'])->name('show');
    });


// ── ÉLÈVES ───────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:view-students'])
    ->prefix('students')
    ->name('students.')
    ->group(function () {
        // Documents & impressions (avant wildcards)
        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('/', [StudentDocumentController::class, 'index'])->name('index');
            Route::get('/cartes', [StudentDocumentController::class, 'bulkCards'])->name('cards');
            Route::get('/certificats', [StudentDocumentController::class, 'bulkCertificates'])->name('certificates');
            Route::get('/fiches', [StudentDocumentController::class, 'bulkInformationSheets'])->name('information-sheets');
            Route::get('/livrets', [StudentDocumentController::class, 'bulkBooklets'])->name('booklets');
            Route::get('/listes', [StudentDocumentController::class, 'bulkLists'])->name('lists');
            Route::get('/rapport-effectifs', [StudentDocumentController::class, 'enrollmentTotalsReport'])->name('enrollment-totals-report');
        });

        Route::get('/', [StudentController::class, 'index'])->name('index');

        Route::get('/{student}/documents/{type}', [StudentDocumentController::class, 'single'])
            ->where('type', 'fiche|certificat|carte|livret')
            ->name('documents.single');

        Route::middleware('permission:manage-students')->group(function () {
            // Inscriptions (avant wildcards)
            Route::get('/{student}/enroll',
                [StudentController::class, 'enroll'])->name('enroll');
            Route::post('/{student}/enroll',
                [StudentController::class, 'storeEnrollment'])
                ->name('enroll.store');
            Route::patch('/enrollments/{enrollment}/transfer',
                [StudentController::class, 'transfer'])
                ->name('enrollments.transfer');
            Route::patch('/enrollments/{enrollment}/status',
                [StudentController::class, 'updateStatus'])
                ->name('enrollments.status');
            Route::delete('/{student}/photo',
                [StudentController::class, 'deletePhoto'])
                ->name('photo.delete');
            
            // CRUD
            Route::get('/create',
                [StudentController::class, 'create'])->name('create');
            Route::post('/',
                [StudentController::class, 'store'])->name('store');
            Route::get('/{student}/edit',
                [StudentController::class, 'edit'])->name('edit');
            Route::put('/{student}',
                [StudentController::class, 'update'])->name('update');
            Route::delete('/{student}',
                [StudentController::class, 'destroy'])->name('destroy');
        });

        Route::get('/{student}',
            [StudentController::class, 'show'])->name('show');
    });


// ── FINANCES ─────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'permission:view-finances'])
    ->prefix('finances')
    ->name('finances.')
    ->group(function () {
        Route::get('/', [FinanceController::class, 'index'])
             ->name('index');
        Route::get('/global', [FinanceController::class, 'global'])
             ->middleware(['role:super-admin,directeur,fondateur'])
             ->name('global');
        Route::get('/payments', [FinanceController::class, 'payments'])
             ->name('payments');
        Route::get('/receipts/batch', [FinanceController::class, 'batchReceipts'])
            ->name('receipts.batch');
        Route::get('/receipts/{payment}', [FinanceController::class, 'receipt'])
            ->name('receipt');
        Route::get('/students/{enrollment}/receipt', [FinanceController::class, 'globalReceipt'])
            ->name('student.receipt');
        Route::get('/students/{enrollment}', [FinanceController::class, 'studentAccount'])
            ->name('student');
        Route::get('/classes/{classGroup}/students', [FinanceController::class, 'classStudents'])
            ->name('class-students');
        Route::get('/fees-list', [FinanceController::class, 'feesList'])
            ->name('fees-list');
        Route::get('/reports', [FinanceController::class, 'reports'])
            ->name('reports');

        Route::middleware('permission:manage-finances')->group(function () {
            Route::get('/fees/{classGroup}',
                [FinanceController::class, 'configureFees'])
                ->name('fees');
            Route::post('/fees/{classGroup}',
                [FinanceController::class, 'saveFees'])
                ->name('fees.save');
            Route::post('/students/{enrollment}/pay',
                [FinanceController::class, 'recordPayment'])
                ->name('pay');
        });
    });



// // ── SECTIONS ──────────────────────────────────────────────────────────────────
// Route::middleware(['auth', 'permission:manage-classes'])
//     ->prefix('sections')
//     ->name('sections.')
//     ->group(function () {
//         Route::get('/',                    [SectionController::class, 'index'])
//              ->name('index');
//         Route::get('/create',              [SectionController::class, 'create'])
//              ->name('create');
//         Route::post('/',                   [SectionController::class, 'store'])
//              ->name('store');
//         Route::get('/{section}',           [SectionController::class, 'show'])
//              ->name('show');
//         Route::get('/{section}/edit',      [SectionController::class, 'edit'])
//              ->name('edit');
//         Route::patch('/{section}',         [SectionController::class, 'update'])
//              ->name('update');
//         Route::delete('/{section}',        [SectionController::class, 'destroy'])
//              ->name('destroy');
//     });

// // ── NIVEAUX ───────────────────────────────────────────────────────────────────
// Route::middleware(['auth', 'permission:manage-levels'])
//     ->prefix('levels')
//     ->name('levels.')
//     ->group(function () {
//         Route::get('/',                    [LevelController::class, 'index'])
//              ->name('index');
//         Route::get('/create',              [LevelController::class, 'create'])
//              ->name('create');
//         Route::post('/',                   [LevelController::class, 'store'])
//              ->name('store');
//         Route::get('/{level}',             [LevelController::class, 'show'])
//              ->name('show');
//         Route::get('/{level}/edit',        [LevelController::class, 'edit'])
//              ->name('edit');
//         Route::patch('/{level}',           [LevelController::class, 'update'])
//              ->name('update');
//         Route::delete('/{level}',          [LevelController::class, 'destroy'])
//              ->name('destroy');
//     });

// // ── CLASSES (CLASS GROUPS) ────────────────────────────────────────────────────
// Route::middleware(['auth', 'permission:manage-classes'])
//     ->prefix('class-groups')
//     ->name('class-groups.')
//     ->group(function () {
//         Route::get('/',                    [ClassGroupController::class, 'index'])
//              ->name('index');
//         Route::get('/create',              [ClassGroupController::class, 'create'])
//              ->name('create');
//         Route::post('/',                   [ClassGroupController::class, 'store'])
//              ->name('store');
//         Route::get('/{classGroup}',        [ClassGroupController::class, 'show'])
//              ->name('show');
//         Route::get('/{classGroup}/edit',   [ClassGroupController::class, 'edit'])
//              ->name('edit');
//         Route::patch('/{classGroup}',      [ClassGroupController::class, 'update'])
//              ->name('update');
//         Route::delete('/{classGroup}',     [ClassGroupController::class, 'destroy'])
//              ->name('destroy');
//     });
