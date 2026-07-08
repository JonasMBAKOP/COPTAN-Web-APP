@extends('layouts.app')

@section('title', 'Documents élèves')

@section('breadcrumb')
    <a href="{{ route('students.index') }}" class="hover:text-gray-700">Élèves</a>
    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="font-medium text-gray-700">Documents &amp; impressions</span>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="documentsHub()">

    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Impressions élèves</h1>
            <p class="text-sm text-gray-500 mt-1">
                Sélectionnez une année, un périmètre, puis ouvrez le document à imprimer.
            </p>
        </div>
        @if($year)
            <div class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-600">
                <span class="w-2 h-2 rounded-full" style="background-color:#1A5C2A;"></span>
                Année active : <strong class="text-gray-900">{{ $year->label }}</strong>
            </div>
        @endif
    </div>

    @if(!$year)
    <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-sm">
        Aucune année scolaire active. Sélectionnez une année ou activez une année scolaire.
    </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 space-y-4">
        <div class="flex items-center justify-between gap-3 border-b border-gray-100 pb-3">
            <h2 class="text-sm font-bold uppercase tracking-wider text-[#1A3A6B]">Périmètre d'impression</h2>
            <span class="text-xs text-gray-500">Ces filtres s'appliquent à tous les documents ci-dessous.</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Année scolaire</label>
                <select x-model="yearId" @change="onYearChange()"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white">
                    @foreach($years as $y)
                    <option value="{{ $y->id }}" {{ $year?->id == $y->id ? 'selected' : '' }}>
                        {{ $y->label }}{{ $y->is_active ? ' (Active)' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Périmètre</label>
                <select x-model="scope" @change="onScopeChange()"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white">
                    <option value="class">Une classe</option>
                    <option value="section">Une section (toutes les classes)</option>
                    <option value="school">Tout l'établissement</option>
                </select>
            </div>

            <div x-show="scope === 'class'" x-cloak>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Classe</label>
                <select x-model="classId"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white">
                    <option value="">Sélectionner...</option>
                    <template x-for="cls in filteredClasses" :key="cls.id">
                        <option :value="cls.id" x-text="cls.label"></option>
                    </template>
                </select>
            </div>

            <div x-show="scope === 'section'" x-cloak>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Section</label>
                <select x-model="sectionId"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white">
                    <option value="">Sélectionner...</option>
                    @foreach($sections as $section)
                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach([
            ['route' => 'students.documents.cards', 'icon' => 'card', 'title' => 'Cartes scolaires', 'desc' => '8 cartes par page A4 (portrait), format identité.', 'scope' => 'Classe, section ou établissement'],
            ['route' => 'students.documents.certificates', 'icon' => 'certificate', 'title' => 'Certificats de scolarité', 'desc' => 'Un certificat par élève, une page chacun.', 'scope' => 'Classe, section ou établissement'],
            ['route' => 'students.documents.information-sheets', 'icon' => 'form', 'title' => 'Fiches de renseignement', 'desc' => 'Une fiche complète par élève, avec champs bilingues.', 'scope' => 'Classe, section ou établissement'],
            ['route' => 'students.documents.booklets', 'icon' => 'book', 'title' => 'Livrets scolaires', 'desc' => 'Structure matières/séquences (notes à venir).', 'scope' => 'Classe uniquement', 'class_only' => true],
            ['route' => 'students.documents.lists', 'icon' => 'list', 'title' => 'Listes des élèves', 'desc' => 'Par classe, section ou établissement entier.', 'scope' => 'Classe, section ou établissement'],
            ['route' => 'students.documents.enrollment-totals-report', 'icon' => 'chart', 'title' => 'Rapport des effectifs', 'desc' => 'Totaux F, G et T par section et pour tout l’établissement.', 'scope' => 'Section ou établissement', 'section_only' => true],
        ] as $doc)
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:border-[#1A3A6B]/40 hover:shadow-md transition-all flex flex-col min-h-[188px]">
            <div class="flex items-start gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center flex-shrink-0" style="color:#1A3A6B;">
                    @switch($doc['icon'])
                        @case('card')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect x="3" y="5" width="18" height="14" rx="2" stroke-width="2"/>
                                <path d="M7 9h6M7 13h4M15 13h2" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            @break
                        @case('certificate')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M6 3h9l3 3v15H6z" stroke-width="2" stroke-linejoin="round"/>
                                <path d="M9 10h6M9 14h6M9 18h3" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            @break
                        @case('form')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M8 4h8l2 2v14H6V6z" stroke-width="2" stroke-linejoin="round"/>
                                <path d="M9 9h6M9 13h6M9 17h3" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            @break
                        @case('book')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M5 4h10a3 3 0 0 1 3 3v13H8a3 3 0 0 0-3-3z" stroke-width="2" stroke-linejoin="round"/>
                                <path d="M8 8h6M8 12h5" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            @break
                        @case('list')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M8 6h12M8 12h12M8 18h12" stroke-width="2" stroke-linecap="round"/>
                                <path d="M4 6h.01M4 12h.01M4 18h.01" stroke-width="3" stroke-linecap="round"/>
                            </svg>
                            @break
                        @case('chart')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M4 19V5M4 19h16" stroke-width="2" stroke-linecap="round"/>
                                <path d="M8 16v-5M12 16V8M16 16v-8" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            @break
                    @endswitch
                </div>
                <div class="min-w-0">
                    <h3 class="font-bold text-gray-900 leading-snug">{{ $doc['title'] }}</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $doc['desc'] }}</p>
                </div>
            </div>
            <div class="mb-4">
                <span class="inline-flex items-center rounded-md border border-gray-200 bg-gray-50 px-2 py-1 text-[11px] font-semibold text-gray-600">
                    {{ $doc['scope'] }}
                </span>
            </div>
            <div class="mt-auto space-y-2">
                <button type="button"
                        @click="openPrint('{{ route($doc['route']) }}', {{ ($doc['section_only'] ?? false) ? 'true' : 'false' }}, {{ ($doc['class_only'] ?? false) ? 'true' : 'false' }})"
                        class="w-full py-2.5 rounded-lg text-sm font-semibold text-white transition-all inline-flex items-center justify-center gap-2"
                        style="background-color:#1A3A6B;"
                        :class="{ 'opacity-60 cursor-not-allowed': !isDocumentActionAllowed('{{ $doc['route'] }}') }"
                        :disabled="!isDocumentActionAllowed('{{ $doc['route'] }}')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V4h12v5M6 18H5a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-1M7 14h10v6H7z"/>
                    </svg>
                    Ouvrir l'aperçu &amp; imprimer
                </button>
                @if(in_array($doc['route'], ['students.documents.lists', 'students.documents.enrollment-totals-report'], true))
                <button type="button"
                        @click="openWordExport('{{ ($doc['route'] === 'students.documents.lists') ? route('students.documents.lists.word') : route('students.documents.enrollment-totals-report.word') }}')"
                        class="w-full py-2.5 rounded-lg text-sm font-semibold text-[#1A3A6B] border border-[#1A3A6B] bg-white transition-all inline-flex items-center justify-center gap-2"
                        :class="{ 'opacity-60 cursor-not-allowed': !isDocumentActionAllowed('{{ $doc['route'] }}') }"
                        :disabled="!isDocumentActionAllowed('{{ $doc['route'] }}')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h6m-6 4h10M5 4h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1Z"/>
                    </svg>
                    Exporter Word
                </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
const _allClasses = {!! json_encode($classesJson) !!};

function documentsHub() {
    return {
        yearId: '{{ $year?->id ?? '' }}',
        scope: 'class',
        classId: '',
        sectionId: '',

        get filteredClasses() {
            if (!this.yearId) return [];
            return _allClasses.filter(c => String(c.year_id) === String(this.yearId));
        },

        onYearChange() {
            this.classId = '';
        },

        onScopeChange() {
            this.classId = '';
            this.sectionId = '';
        },

        buildQuery() {
            const p = new URLSearchParams();
            if (this.yearId) p.set('year_id', this.yearId);
            p.set('scope', this.scope);
            if (this.scope === 'class' && this.classId) p.set('class_id', this.classId);
            if (this.scope === 'section' && this.sectionId) p.set('section_id', this.sectionId);
            return p.toString();
        },

        isDocumentActionAllowed(routeName) {
            if (routeName === 'students.documents.enrollment-totals-report') {
                return this.scope === 'section' ? !!this.sectionId : this.scope === 'school';
            }

            if (routeName === 'students.documents.booklets') {
                return this.scope === 'class' && !!this.classId;
            }

            if (this.scope === 'class') {
                return !!this.classId;
            }

            if (this.scope === 'section') {
                return !!this.sectionId;
            }

            return this.scope === 'school';
        },

        openPrint(baseUrl, sectionOnly = false, classOnly = false) {
            if (!this.yearId) {
                alert('Veuillez sélectionner une année scolaire.');
                return;
            }
            if (!this.isDocumentActionAllowed('students.documents.enrollment-totals-report') && baseUrl.includes('enrollment-totals-report')) {
                alert("Le rapport des effectifs s'imprime uniquement par section ou pour tout l'établissement.");
                return;
            }
            if (classOnly && this.scope !== 'class') {
                alert('La génération des livrets scolaires ne fonctionne que pour une seule classe à la fois. Sélectionnez une classe.');
                return;
            }
            if (sectionOnly && this.scope === 'class') {
                alert("Le rapport des effectifs s'imprime uniquement par section ou pour tout l'établissement. Sélectionnez une section ou le périmètre établissement.");
                return;
            }
            if (!this.isDocumentActionAllowed('students.documents.' + (baseUrl.includes('enrollment-totals-report') ? 'enrollment-totals-report' : 'lists'))) {
                alert('Veuillez sélectionner les filtres requis avant d’ouvrir ce document.');
                return;
            }
            window.open(baseUrl + '?' + this.buildQuery(), '_blank');
        },

        openWordExport(baseUrl) {
            if (!this.yearId) {
                alert('Veuillez sélectionner une année scolaire.');
                return;
            }
            if (!this.isDocumentActionAllowed('students.documents.enrollment-totals-report') && baseUrl.includes('enrollment-totals-report')) {
                alert("L'export Word du rapport des effectifs est disponible uniquement pour une section ou pour tout l'établissement.");
                return;
            }
            if (!this.isDocumentActionAllowed('students.documents.lists') && !baseUrl.includes('enrollment-totals-report')) {
                alert('Veuillez sélectionner les filtres requis avant d’exporter ce document.');
                return;
            }
            window.open(baseUrl + '?' + this.buildQuery(), '_blank');
        },
    };
}
</script>
@endsection
