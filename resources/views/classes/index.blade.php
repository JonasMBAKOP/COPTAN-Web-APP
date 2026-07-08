@extends('layouts.app')

@section('title', 'Gestion des Classes')
@section('page-title', 'Gestions des Classes')
@section('page-subtitle', 'Organisation et suivi des effectifs par section et niveau d\'enseignement')

{{-- @section('breadcrumb')
    <a href="{{ route('settings.index') }}" class="hover:text-gray-700">Paramètres</a>
    <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="font-medium" style="color: #1A3A6B;">Gestion des Classes</span>
@endsection --}}

@section('content')

{{-- CALCUL STATISTIQUES RÉELLES --}}
@php
    $totalMaxStudents = 0;
    if (!($isTeacher ?? false)) {
        foreach($sections as $section) {
            $sectionClasses = $classGroups->get($section->id, collect());
            $totalMaxStudents += $sectionClasses->sum('max_students');
        }
    }
    $occupationRate = $totalMaxStudents > 0 ? round(($stats['total_students'] / $totalMaxStudents) * 100) : 0;
@endphp

<div x-data="{
    search: '',
    selectedSection: '',
    selectedLevel: '',
    openSections: {
        @foreach($sections as $section)
            '{{ $section->id }}': {{ $loop->first ? 'true' : 'false' }},
        @endforeach
    }
}">


    {{-- ── CARTES DE STATISTIQUES GLOBALEs EN HAUT ─────────────────────── --}}
    <div class="grid grid-cols-1 {{ ($isTeacher ?? false) ? 'md:grid-cols-2' : 'md:grid-cols-3' }} gap-6 mt-1 mb-6">
        {{-- Total Classes --}}
        <div class="rounded-2xl p-6 text-white flex items-center justify-between shadow-sm transition-transform duration-200 hover:scale-[1.01]" style="background-color: #1A3A6B;">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider opacity-70 mb-2">Total Classes</p>
                <p class="text-4xl font-black">{{ $stats['total_classes'] }}</p>
            </div>
            <div class="bg-white/10 p-3 rounded-xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
            </div>
        </div>

        {{-- Étudiants Actifs --}}
        <div class="bg-white border border-gray-150 rounded-2xl p-6 flex items-center justify-between shadow-sm transition-transform duration-200 hover:scale-[1.01]">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Étudiants Actifs</p>
                <p class="text-4xl font-black" style="color: #1A3A6B;">{{ number_format($stats['total_students']) }}</p>
            </div>
            <div class="bg-gray-50 p-3 rounded-xl" style="color: #1A3A6B;">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>

        {{-- Occupation Moyenne (administration uniquement) --}}
        @unless($isTeacher ?? false)
        <div class="bg-white border border-gray-150 rounded-2xl p-6 flex items-center justify-between shadow-sm transition-transform duration-200 hover:scale-[1.01]">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Occupation Moyenne</p>
                <p class="text-4xl font-black" style="color: #1A3A6B;">{{ $occupationRate }}%</p>
            </div>
            <div class="relative flex items-center justify-center">
                <svg class="w-16 h-16 transform -rotate-90">
                    <circle cx="32" cy="32" r="26" stroke="#F3F4F6" stroke-width="6" fill="transparent" />
                    <circle cx="32" cy="32" r="26" stroke="#A24E0C" stroke-width="6" fill="transparent"
                            stroke-dasharray="163.3" stroke-dashoffset="{{ 163.3 * (1 - $occupationRate / 100) }}" />
                </svg>
                <span class="absolute text-xs font-black" style="color: #A24E0C;">{{ $occupationRate }}%</span>
            </div>
        </div>
        @endunless
    </div>

    {{-- ── ALERTE : PAS D'ANNÉE ACTIVE ────────────────────────────────────── --}}
    @if(!$selectedYear)
    <div class="flex items-start gap-4 p-4 rounded-2xl mb-6 bg-amber-50 border border-amber-200 shadow-sm">
        <svg class="w-6 h-6 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <p class="text-sm font-bold text-amber-800">
                Aucune année scolaire active
            </p>
            <p class="text-xs text-amber-700 mt-1">
                Activez une année scolaire depuis les paramètres pour pouvoir ajouter ou gérer les classes.
                <a href="{{ route('academic-years.index') }}" class="underline font-bold hover:text-amber-900 ml-1">
                    Gérer les années scolaires →
                </a>
            </p>
        </div>
    </div>
    @endif

    {{-- ── BARRE DE FILTRES DYNAMIQUE (MOCKUP 1) ────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-150 p-5 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Sélection de l'année --}}
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Année scolaire</label>
                @if($isTeacher ?? false)
                <div class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm font-semibold bg-gray-50"
                     style="color:#1A3A6B;">
                    {{ $selectedYear?->label ?? '—' }}
                    @if($selectedYear?->is_active)
                    <span class="text-xs font-bold text-green-600 ml-1">(Active)</span>
                    @endif
                </div>
                @else
                <form method="GET" action="{{ route('classes.index') }}">
                    <select name="year_id" onchange="this.form.submit()"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white font-medium"
                            style="color: #1A3A6B;">
                        @foreach($years as $year)
                        <option value="{{ $year->id }}" {{ $selectedYear?->id === $year->id ? 'selected' : '' }}>
                            {{ $year->label }} {{ $year->is_active ? '(Active)' : '' }}
                        </option>
                        @endforeach
                    </select>
                </form>
                @endif
            </div>

            {{-- Sélection de la section --}}
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Section</label>
                <select x-model="selectedSection"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white font-medium"
                        style="color: #1A3A6B;">
                    <option value="">Toutes</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}">{{ $section->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Sélection du niveau --}}
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Niveau</label>
                <select x-model="selectedLevel"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white font-medium"
                        style="color: #1A3A6B;">
                    <option value="">Tous les niveaux</option>
                    @foreach($sections as $sec)
                        <optgroup label="{{ $sec->name }}" x-show="selectedSection === '' || selectedSection == '{{ $sec->id }}'">
                            @foreach($sec->levels as $lev)
                                @php
                                    $levelHasClass = ($classGroups->get($sec->id, collect()))
                                        ->contains(fn ($c) => (int) $c->level_id === (int) $lev->id);
                                @endphp
                                @if(!($isTeacher ?? false) || $levelHasClass)
                                <option value="{{ $lev->id }}">{{ $lev->name }}</option>
                                @endif
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            {{-- Recherche textuelle --}}
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Rechercher une classe</label>
                <div class="relative">
                    <input type="text" x-model="search" placeholder="Ex: 3ème B, Terminale..."
                           class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white font-medium"
                           style="color: #1A3A6B;" />
                    <div class="absolute left-4 top-3.5 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── GRILLE PAR SECTION ─────────────────────────────────────────── --}}
    @if($selectedYear)
        @if(($isTeacher ?? false) && $sections->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 p-10 text-center text-gray-500">
            <p class="font-semibold">Aucune classe ne vous est affectée pour {{ $selectedYear->label }}.</p>
        </div>
        @endif
        @foreach($sections as $section)
        @php
            $sectionClasses = $classGroups->get($section->id, collect());
        @endphp
        
        <div class="mb-5 bg-white rounded-2xl shadow-sm border border-gray-150 overflow-hidden"
             x-show="selectedSection === '' || selectedSection == '{{ $section->id }}'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100">
            
            {{-- En-tête de l'accordéon --}}
            <button type="button" @click="openSections['{{ $section->id }}'] = !openSections['{{ $section->id }}']"
                    class="w-full px-6 py-4.5 flex items-center justify-between text-white font-bold text-base transition-all duration-200 focus:outline-none"
                    style="background-color: #1A3A6B;">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span class="tracking-wide">{{ strtoupper($section->name) }}</span>
                    <span class="ml-2 bg-white/20 px-2.5 py-0.5 rounded-full text-xs font-bold">
                        {{ $sectionClasses->count() }} classe(s)
                    </span>
                </div>
                <svg class="w-5 h-5 transition-transform duration-300"
                     :class="openSections['{{ $section->id }}'] ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- Contenu de l'accordéon --}}
            <div x-show="openSections['{{ $section->id }}']" x-collapse>
                <div class="p-6">
                    @if($sectionClasses->isEmpty())
                    <div class="text-center py-8 text-gray-400 italic text-sm">
                        Aucune classe configurée dans cette section pour {{ $selectedYear->label }}.
                    </div>
                    @else
                        {{-- Grille unifiée de classes (alignées horizontalement, 5/6 par ligne sans en-têtes de niveaux) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                            @foreach($sectionClasses->sortBy('level.order_index')->sortBy('name') as $class)
                            @php
                                $pct = $class->max_students > 0 ? round(($class->student_enrollments_count / $class->max_students) * 100) : 0;
                                $isFull = $pct >= 100;
                            @endphp
                            
                            <div class="bg-white border rounded-2xl p-5 hover:shadow-sm hover:border-blue-200 transition-all duration-200 group flex flex-col justify-between"
                                 :class="selectedLevel === '' || selectedLevel == '{{ $class->level_id }}' ? '' : 'hidden'"
                                 x-show="search === '' || '{{ strtolower($class->full_name) }}'.includes(search.toLowerCase()) || '{{ strtolower($class->titularStaff?->full_name ?? '') }}'.includes(search.toLowerCase())"
                                 style="border-color: {{ $isFull ? '#A24E0C' : '#E5E7EB' }}; border-left-width: 4px; border-left-color: {{ $isFull ? '#A24E0C' : '#1A3A6B' }};">
                                
                                {{-- En-tête de carte --}}
                                <div>
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <h4 class="font-extrabold text-lg leading-tight" style="color: #1A3A6B;">
                                                {{ $class->full_name }}
                                            </h4>
                                            <p class="text-[10px] font-bold text-gray-400 mt-0.5 uppercase tracking-wider">
                                                {{ $class->level->name }}
                                            </p>
                                        </div>
                                        
                                        {{-- Actions contextuelles --}}
                                        <div class="flex items-center gap-1 opacity-20 group-hover:opacity-100 transition-opacity duration-200">
                                            <a href="{{ route('classes.show', $class) }}" 
                                               class="p-1 text-gray-400 hover:text-blue-700 rounded transition-colors"
                                               title="Consulter">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            
                                            @can('manage-classes')
                                            @if(!$selectedYear->isClosed())
                                            <a href="{{ route('classes.edit', $class) }}" 
                                               class="p-1 text-gray-400 hover:text-amber-600 rounded transition-colors"
                                               title="Modifier">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                            
                                            @if($class->student_enrollments_count === 0)
                                            <form method="POST" action="{{ route('classes.destroy', $class) }}" 
                                                  onsubmit="return confirm('Supprimer définitivement la classe {{ $class->full_name }} ?')" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-1 text-gray-400 hover:text-red-600 rounded transition-colors" title="Supprimer">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                            @endif
                                            @endif
                                            @endcan
                                        </div>
                                    </div>

                                    {{-- Détails --}}
                                    <div class="space-y-2 mb-4 text-xs font-medium text-gray-500">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-450 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span class="truncate text-gray-750">{{ $class->titularStaff?->full_name ?? 'Pas de titulaire' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-450 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                            <span class="text-gray-700">{{ $class->class_subjects_count }} matières</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Barre de progression et occupation --}}
                                @unless($isTeacher ?? false)
                                <div class="mt-2">
                                    <div class="flex justify-between items-center text-xs mb-1.5">
                                        @if($isFull)
                                            <span class="font-extrabold text-[11px]" style="color: #A24E0C;">COMPLET: {{ $class->student_enrollments_count }} / {{ $class->max_students }}</span>
                                            <span class="font-extrabold" style="color: #A24E0C;">100%</span>
                                        @else
                                            <span class="text-gray-500 font-semibold">Effectif: {{ $class->student_enrollments_count }} / {{ $class->max_students }}</span>
                                            <span class="font-extrabold text-gray-700">{{ $pct }}%</span>
                                        @endif
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full" style="height: {{ $isFull ? '6px' : '4px' }};">
                                        <div class="h-full rounded-full transition-all duration-500"
                                             style="width: {{ $pct }}%; background-color: {{ $isFull ? '#A24E0C' : '#1A5C2A' }};">
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="mt-2 text-xs font-semibold text-gray-500">
                                    Effectif : {{ $class->student_enrollments_count }} élève(s)
                                </div>
                                @endunless
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    @endif

    {{-- ── BOUTON FLOTANT "NOUVELLE CLASSE" ─────────────────────────────── --}}
    @can('manage-classes')
        @if($activeYear && $selectedYear?->isClosed() === false)
        <a href="{{ route('classes.create') }}"
           class="group fixed bottom-3 right-3 sm:bottom-4 sm:right-4 md:bottom-5 md:right-5 lg:bottom-6 lg:right-6
                  z-50 flex items-center justify-center gap-1.5 sm:gap-2
                  w-12 h-12 sm:w-13 sm:h-13 md:w-14 md:h-14 lg:w-16 lg:h-16
                  rounded-full shadow-lg transition-all duration-300
                  hover:w-auto hover:px-3 sm:hover:px-4 md:hover:px-5 lg:hover:px-6
                  hover:pr-3 sm:hover:pr-4 md:hover:pr-5 lg:hover:pr-6
                  text-white font-semibold text-xs sm:text-xs md:text-sm lg:text-sm
                  hover:shadow-xl hover:scale-105 active:scale-95"
           style="background-color: #E87722;">

            <svg class="w-5 h-5 sm:w-5 sm:h-5 md:w-6 md:h-6 lg:w-7 lg:h-7
                        group-hover:hidden transition-all duration-300"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>

            <span class="hidden group-hover:flex items-center gap-1 sm:gap-1.5 md:gap-2 lg:gap-2
                         transition-all duration-300 whitespace-nowrap">
                <svg class="w-4 h-4 sm:w-4 sm:h-4 md:w-5 md:h-5 lg:w-6 lg:h-6"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="text-xs sm:text-xs md:text-sm lg:text-sm">
                    Nouvelle classe
                </span>
            </span>
        </a>
        @endif
    @endcan

</div>

@endsection
