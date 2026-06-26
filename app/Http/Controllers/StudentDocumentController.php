<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\StudentDocumentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentDocumentController extends Controller
{
    public function __construct(
        private readonly StudentDocumentService $documents
    ) {}

    public function index(Request $request): View
    {
        $year    = $this->documents->yearFromRequest($request->integer('year_id') ?: null);
        $years   = \App\Models\AcademicYear::orderByDesc('start_date')->get();
        $options     = $this->documents->filterOptions($year);
        $classesJson = $this->documents->classesJsonForHub();

        return view('students.documents.index', array_merge(
            $this->documents->schoolContext(),
            $options,
            compact('year', 'years', 'classesJson')
        ));
    }

    public function single(Request $request, Student $student, string $type)
    {
        $year       = $this->documents->yearFromRequest($request->integer('year_id') ?: null);
        $enrollment = $this->documents->enrollmentForStudent($student, $year);

        abort_if(! $enrollment && in_array($type, ['certificat', 'carte', 'livret'], true), 404,
            'Aucune inscription active trouvée pour cet élève.');

        if ($type === 'livret') {
            return redirect()->route('livrets.show', $enrollment);
        }

        return $this->renderDocument($type, collect([$student]), $year, $enrollment, $student);
    }

    public function bulkCards(Request $request): View
    {
        return $this->bulk($request, 'cartes');
    }

    public function bulkCertificates(Request $request): View
    {
        return $this->bulk($request, 'certificats');
    }

    public function bulkInformationSheets(Request $request): View
    {
        return $this->bulk($request, 'fiches');
    }

    public function bulkBooklets(Request $request)
    {
        $year    = $this->documents->yearFromRequest($request->integer('year_id') ?: null);
        $filters = $this->filtersFromRequest($request);

        abort_if(! $year, 422, 'Aucune année scolaire sélectionnée.');

        $classId = $filters['class_id'];
        abort_if(!$classId, 422, 'Aucune classe sélectionnée.');

        $students = $this->documents->getStudentsForPrint($year, $filters);
        $enrollmentIds = \App\Models\StudentEnrollment::whereIn('student_id', $students->pluck('id'))
            ->where('class_group_id', $classId)
            ->where('academic_year_id', $year->id)
            ->pluck('id')
            ->toArray();

        return redirect()->route('livrets.bulk', [
            'class_group_id' => $classId,
            'student_ids' => $enrollmentIds,
        ]);
    }

    public function bulkLists(Request $request): View
    {
        $year    = $this->documents->yearFromRequest($request->integer('year_id') ?: null);
        $filters = $this->filtersFromRequest($request);

        abort_if(! $year, 422, 'Aucune année scolaire sélectionnée.');

        $groups = $this->documents->getListGroups($year, $filters);

        abort_if($groups === [], 404, 'Aucun élève trouvé pour cette sélection.');

        return view('students.documents.bulk.listes', array_merge(
            $this->documents->schoolContext(),
            compact('year', 'groups', 'filters')
        ));
    }

    public function enrollmentTotalsReport(Request $request): View
    {
        $year    = $this->documents->yearFromRequest($request->integer('year_id') ?: null);
        $filters = $this->filtersFromRequest($request);

        abort_if(! $year, 422, 'Aucune année scolaire sélectionnée.');
        abort_if(($filters['scope'] ?? null) === 'class', 422,
            'Le rapport des effectifs s\'imprime uniquement par section ou pour tout l\'établissement.');

        $report = $this->documents->getEnrollmentTotalsReport($year, $filters);

        abort_if($report['sections'] === [], 404, 'Aucune classe trouvée pour cette sélection.');

        return view('students.documents.reports.effectifs-totaux', array_merge(
            $this->documents->schoolContext(),
            compact('year', 'filters', 'report')
        ));
    }

    private function bulk(Request $request, string $type): View
    {
        $year    = $this->documents->yearFromRequest($request->integer('year_id') ?: null);
        $filters = $this->filtersFromRequest($request);

        abort_if(! $year, 422, 'Aucune année scolaire sélectionnée.');

        $students = $this->documents->getStudentsForPrint($year, $filters);

        abort_if($students->isEmpty(), 404, 'Aucun élève trouvé pour cette sélection.');

        return $this->renderDocument($type, $students, $year, null, null, $filters);
    }

    private function filtersFromRequest(Request $request): array
    {
        return [
            'scope'      => $request->input('scope', 'class'),
            'class_id'   => $request->integer('class_id') ?: null,
            'section_id' => $request->integer('section_id') ?: null,
        ];
    }

    private function renderDocument(
        string $type,
        $students,
        $year,
        $enrollment = null,
        ?Student $student = null,
        array $filters = []
    ): View {
        $viewMap = [
            'fiche'       => 'students.documents.fiche-renseignement',
            'certificat'  => 'students.documents.certificat-scolarite',
            'carte'       => 'students.documents.carte-scolaire',
            'cartes'      => 'students.documents.bulk.cartes',
            'certificats' => 'students.documents.bulk.certificats',
            'fiches'      => 'students.documents.bulk.fiches',
            'livret'      => 'students.documents.livret-scolaire',
            'livrets'     => 'students.documents.bulk.livrets',
        ];

        abort_unless(isset($viewMap[$type]), 404);

        $data = array_merge($this->documents->schoolContext(), [
            'year'       => $year,
            'students'   => $students,
            'student'    => $student ?? $students->first(),
            'enrollment' => $enrollment ?? ($student
                ? $this->documents->enrollmentForStudent($student, $year)
                : $students->first()?->printEnrollment),
            'filters'    => $filters,
            'sequences'  => $this->documents->sequencesForYear($year),
        ]);

        if (in_array($type, ['livret', 'livrets'], true)) {
            $data['subjectsByEnrollment'] = $students->mapWithKeys(function ($s) use ($year) {
                $enr = $s->printEnrollment
                    ?? $this->documents->enrollmentForStudent($s, $year);

                return [$s->id => $this->documents->subjectsForEnrollment($enr)];
            });
        }

        if ($type === 'fiche') {
            $data['enrollment'] = $enrollment
                ?? $this->documents->enrollmentForStudent($data['student'], $year);
        }

        return view($viewMap[$type], $data);
    }
}
