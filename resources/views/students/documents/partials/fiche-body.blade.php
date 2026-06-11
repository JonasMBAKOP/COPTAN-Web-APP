<div class="fiche-top">
    @include('students.documents.partials.student-photo', ['student' => $student])
    <div class="fiche-identity">
        <div class="fiche-name">{{ $student->full_name }}</div>
        <div class="fiche-matricule">
            <span class="fiche-inline-label">
                <span>Matricule</span>
                <em>Registration No.</em>
            </span>
            <strong>{{ $student->matricule }}</strong>
        </div>
        @if($enrollment)
            <div class="fiche-classline">
                <span class="fiche-inline-label">
                    <span>Classe</span>
                    <em>Class</em>
                </span>
                <strong>{{ $enrollment->classGroup->full_name }}</strong>
                <span class="fiche-inline-label">
                    <span>Section</span>
                    <em>Section</em>
                </span>
                <strong>{{ $enrollment->classGroup->level->section->name }}</strong>
            </div>
        @endif
    </div>
</div>

<div class="fiche-section">
    <div class="fiche-section__title">
        <span>Identité de l'élève</span>
        <span>/</span>
        <em>Student Identity</em>
    </div>
    <div class="fiche-grid">
        <div class="fiche-field">
            <span class="fiche-label">Nom <em>Lastname</em></span>
            <span class="fiche-value">{{ $student->last_name }}</span>
        </div>
        <div class="fiche-field">
            <span class="fiche-label">Prénom(s) <em>Firstname(s)</em></span>
            <span class="fiche-value">{{ $student->first_name }}</span>
        </div>
        <div class="fiche-field">
            <span class="fiche-label">Sexe <em>Gender</em></span>
            <span class="fiche-value">{{ $student->gender === 'M' ? 'Masculin' : 'Féminin' }}</span>
        </div>
        <div class="fiche-field">
            <span class="fiche-label">Date de naissance <em>Date of birth</em></span>
            <span class="fiche-value">{{ $student->date_of_birth?->format('d/m/Y') ?? '—' }}</span>
        </div>
        <div class="fiche-field">
            <span class="fiche-label">Lieu de naissance <em>Place of birth</em></span>
            <span class="fiche-value">{{ $student->place_of_birth ?? '—' }}</span>
        </div>
        <div class="fiche-field">
            <span class="fiche-label">Nationalité <em>Nationality</em></span>
            <span class="fiche-value">{{ $student->nationality ?? '—' }}</span>
        </div>
        <div class="fiche-field">
            <span class="fiche-label">N° acte de naissance <em>Birth certificate No.</em></span>
            <span class="fiche-value">{{ $student->birth_certificate_number ?? '—' }}</span>
        </div>
        <div class="fiche-field">
            <span class="fiche-label">Adresse <em>Address</em></span>
            <span class="fiche-value">{{ $student->address ?? '—' }}</span>
        </div>
    </div>
</div>

@if($enrollment)
    <div class="fiche-section">
        <div class="fiche-section__title">
            <span>Scolarité</span>
            <span>/</span>
            <em>Schooling</em>
        </div>
        <div class="fiche-grid">
            <div class="fiche-field">
                <span class="fiche-label">Année scolaire <em>School year</em></span>
                <span class="fiche-value">{{ $enrollment->academicYear->label }}</span>
            </div>
            <div class="fiche-field">
                <span class="fiche-label">Date d'inscription <em>Enrollment date</em></span>
                <span class="fiche-value">{{ $enrollment->enrollment_date?->format('d/m/Y') }}</span>
            </div>
            <div class="fiche-field">
                <span class="fiche-label">Section <em>Section</em></span>
                <span class="fiche-value">{{ $enrollment->classGroup->level->section->name }}</span>
            </div>
            <div class="fiche-field">
                <span class="fiche-label">Classe <em>Class</em></span>
                <span class="fiche-value">{{ $enrollment->classGroup->full_name }}</span>
            </div>
            <div class="fiche-field">
                <span class="fiche-label">Situation <em>Status</em></span>
                <span class="fiche-value">{{ $enrollment->is_repeating ? 'Redoublant(e)' : 'Nouveau / Promu(e)' }}</span>
            </div>
            <div class="fiche-field">
                <span class="fiche-label">Classe précédente <em>Previous class</em></span>
                <span class="fiche-value">{{ $enrollment->previous_class_label ?? $enrollment->previousClassGroup?->full_name ?? '—' }}</span>
            </div>
            <div class="fiche-field">
                <span class="fiche-label">École d'origine <em>Previous school</em></span>
                <span class="fiche-value">{{ $enrollment->origin_school ?? 'COPTAN' }}</span>
            </div>
            <div class="fiche-field">
                <span class="fiche-label">Statut <em>Enrollment status</em></span>
                <span class="fiche-value">{{ $enrollment->isActive() ? 'Actif(ve)' : ucfirst($enrollment->status) }}</span>
            </div>
        </div>
    </div>
@endif

<div class="fiche-section">
    <div class="fiche-section__title">
        <span>Parents &amp; tuteurs</span>
        <span>/</span>
        <em>Parents &amp; Guardians</em>
    </div>
    <div class="fiche-grid">
        <div class="fiche-field">
            <span class="fiche-label">Père <em>Father</em></span>
            <span class="fiche-value">{{ $student->father_name ?? '—' }}</span>
        </div>
        <div class="fiche-field">
            <span class="fiche-label">Tél. père <em>Father phone</em></span>
            <span class="fiche-value">{{ $student->father_phone ?? '—' }}</span>
        </div>
        <div class="fiche-field">
            <span class="fiche-label">Mère <em>Mother</em></span>
            <span class="fiche-value">{{ $student->mother_name ?? '—' }}</span>
        </div>
        <div class="fiche-field">
            <span class="fiche-label">Tél. mère <em>Mother phone</em></span>
            <span class="fiche-value">{{ $student->mother_phone ?? '—' }}</span>
        </div>
        <div class="fiche-field">
            <span class="fiche-label">Tuteur <em>Guardian</em></span>
            <span class="fiche-value">{{ $student->guardian_name ?? '—' }}</span>
        </div>
        <div class="fiche-field">
            <span class="fiche-label">Tél. tuteur <em>Guardian phone</em></span>
            <span class="fiche-value">{{ $student->guardian_phone ?? '—' }}</span>
        </div>
        <div class="fiche-field fiche-field--wide">
            <span class="fiche-label">Lien de parenté <em>Relationship</em></span>
            <span class="fiche-value">{{ $student->guardian_relationship ?? '—' }}</span>
        </div>
    </div>
</div>
