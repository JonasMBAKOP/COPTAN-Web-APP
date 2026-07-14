@extends('layouts.app')

@section('title', 'Modifier — ' . $student->full_name)
@section('page-title', 'Modifier Fiche Élève')
@section('page-subtitle', 'Formulaire de modification des informations de l\'élève ' . $student->full_name)

@section('breadcrumb')
    <a href="{{ route('students.index') }}" class="hover:text-gray-700">Élèves</a>
    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <a href="{{ route('students.show', $student) }}" class="hover:text-gray-700">{{ $student->full_name }}</a>
    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="font-medium" style="color:#1A3A6B;">Modifier</span>
@endsection

@section('content')

<form method="POST" action="{{ route('students.update', $student) }}"
      enctype="multipart/form-data"
      x-data="editStudentForm()"
      id="edit-student-form">
    @csrf @method('PUT')

    {{-- ── ENTÊTE PAGE ───────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black" style="color:#1A3A6B;">Modifier la fiche élève</h1>
            <p class="text-sm text-gray-400 mt-0.5">{{ $student->matricule }} · Modifié le {{ $student->updated_at->format('d/m/Y') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('students.show', $student) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Annuler
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-bold shadow-sm transition-all hover:shadow-md active:scale-95"
                    style="background-color:#1A3A6B;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Enregistrer
            </button>
        </div>
    </div>

    {{-- ── ERREURS GLOBALES ──────────────────────────────────────────────── --}}
    @if($errors->any())
    <div class="flex items-start gap-3 p-4 mb-5 bg-red-50 border border-red-200 rounded-xl">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-red-700 mb-1">Veuillez corriger les erreurs suivantes :</p>
            <ul class="text-sm text-red-600 space-y-0.5 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- COLONNE GAUCHE : PHOTO + MATRICULE + DOSSIER                  --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        <div class="space-y-5">

            {{-- Photo --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5"
                 x-data="{ preview: '{{ $student->photo ? asset('storage/' . $student->photo) : '' }}' }">

                {{-- Cercle photo cliquable --}}
                <label for="photo-upload"
                       class="block relative w-32 h-32 mx-auto cursor-pointer group">
                    <template x-if="preview">
                        <img :src="preview" alt="Photo"
                             class="w-full h-full rounded-full object-cover ring-4 ring-white shadow-md transition-transform group-hover:scale-105">
                    </template>
                    <template x-if="!preview">
                        <div class="w-full h-full rounded-full flex items-center justify-center ring-4 ring-white shadow-md text-white font-black text-3xl transition-transform group-hover:scale-105"
                             style="background: linear-gradient(135deg, #1A3A6B, #2d5aa0);">
                            {{ strtoupper(substr($student->last_name, 0, 1)) . strtoupper(substr($student->first_name, 0, 1)) }}
                        </div>
                    </template>
                    {{-- Overlay caméra --}}
                    <div class="absolute inset-0 rounded-full bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <input type="file" name="photo" id="photo-upload" class="hidden" accept="image/*"
                           @change="preview = URL.createObjectURL($event.target.files[0])">
                </label>

                <div class="text-center mt-3">
                    <p class="text-xs font-semibold text-gray-700">{{ $student->full_name }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Cliquer pour changer</p>
                </div>
                @if($student->photo)
                <button type="submit" form="delete-student-photo-form" onclick="return confirm('Supprimer la photo ?')"
                            class="w-full py-2 rounded-lg text-xs font-medium text-red-500 border border-red-100 bg-red-50 hover:bg-red-100 transition-colors flex items-center justify-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Supprimer la photo
                    </button>
                @endif
            </div>

            {{-- Matricule --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Matricule</label>
                <div class="flex items-center gap-2 bg-[#EBF3FB] px-3 py-2.5 rounded-lg">
                    <svg class="w-4 h-4 flex-shrink-0" style="color:#1A3A6B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                    </svg>
                    <input type="text" name="matricule"
                           value="{{ old('matricule', $student->matricule) }}"
                           class="flex-1 bg-transparent text-sm font-mono font-bold focus:outline-none min-w-0"
                           style="color:#1A3A6B;">
                </div>
                @error('matricule')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Fiche dossier --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">Informations dossier</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <dt class="text-gray-400">Créé le</dt>
                        <dd class="font-semibold text-gray-700">{{ $student->created_at->format('d/m/Y') }}</dd>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <dt class="text-gray-400">Inscriptions</dt>
                        <dd>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                {{ $student->enrollments()->count() }} année(s)
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <dt class="text-gray-400">Statut</dt>
                        <dd>
                            @php
                                $activeEnr = $student->enrollments()->where('status','active')->first();
                            @endphp
                            @if($activeEnr)
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">Actif(ve)</span>
                            @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-500">Non inscrit</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Bouton enregistrer (mobile) --}}
            <div class="lg:hidden">
                <button type="submit"
                        class="w-full py-3.5 rounded-xl text-white font-bold text-sm flex items-center justify-center gap-2 transition-all hover:shadow-md"
                        style="background-color:#1A3A6B;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Enregistrer les modifications
                </button>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- COLONNES CENTRALES : FORMULAIRE                               --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        <div class="lg:col-span-3 space-y-5">

            {{-- ── Section 1 : Identité ────────────────────────────────── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                {{-- En-tête section --}}
                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
                     style="background: linear-gradient(to right, #EBF3FB, #f8fafc);">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background-color:#1A3A6B;">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold" style="color:#1A3A6B;">Identité de l'élève</h2>
                        <p class="text-xs text-gray-400">Informations civiles et personnelles</p>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        {{-- Nom --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                                Nom de famille <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="last_name"
                                   value="{{ old('last_name', $student->last_name) }}"
                                   placeholder="NOM"
                                   class="w-full px-4 py-2.5 border rounded-xl text-sm font-semibold uppercase
                                          transition-all focus:outline-none focus:ring-2 focus:ring-[#1A3A6B]/20 focus:border-[#1A3A6B]
                                          @error('last_name') border-red-400 bg-red-50 @else border-gray-200 hover:border-gray-300 @enderror">
                            @error('last_name')
                            <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Prénom --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                                Prénom(s) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="first_name"
                                   value="{{ old('first_name', $student->first_name) }}"
                                   placeholder="Prénom(s)"
                                   class="w-full px-4 py-2.5 border rounded-xl text-sm
                                          transition-all focus:outline-none focus:ring-2 focus:ring-[#1A3A6B]/20 focus:border-[#1A3A6B]
                                          @error('first_name') border-red-400 bg-red-50 @else border-gray-200 hover:border-gray-300 @enderror">
                            @error('first_name')
                            <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Genre --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                                Genre <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-3">
                                @foreach(['M' => ['label' => 'Masculin', 'color' => '#1A3A6B'], 'F' => ['label' => 'Féminin', 'color' => '#BE185D']] as $val => $cfg)
                                <label class="flex-1 flex items-center gap-2.5 px-4 py-2.5 border-2 rounded-xl cursor-pointer transition-all text-sm font-medium
                                             {{ old('gender', $student->gender) === $val ? 'border-[#1A3A6B] bg-blue-50/60 text-[#1A3A6B] font-semibold' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                                    <input type="radio" name="gender" value="{{ $val }}"
                                           {{ old('gender', $student->gender) === $val ? 'checked' : '' }}
                                           class="text-[#1A3A6B] focus:ring-[#1A3A6B]">
                                    {{ $cfg['label'] }}
                                </label>
                                @endforeach
                            </div>
                            @error('gender')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nationalité --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Nationalité</label>
                            <input type="text" name="nationality"
                                   value="{{ old('nationality', $student->nationality) }}"
                                   placeholder="Ex: Camerounaise"
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm hover:border-gray-300
                                          transition-all focus:outline-none focus:ring-2 focus:ring-[#1A3A6B]/20 focus:border-[#1A3A6B]">
                        </div>

                        {{-- Date naissance --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                                Date de naissance <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="date_of_birth"
                                   value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}"
                                   class="w-full px-4 py-2.5 border rounded-xl text-sm
                                          transition-all focus:outline-none focus:ring-2 focus:ring-[#1A3A6B]/20 focus:border-[#1A3A6B]
                                          @error('date_of_birth') border-red-400 bg-red-50 @else border-gray-200 hover:border-gray-300 @enderror">
                            @error('date_of_birth')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Lieu de naissance --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Lieu de naissance</label>
                            <input type="text" name="place_of_birth"
                                   value="{{ old('place_of_birth', $student->place_of_birth) }}"
                                   placeholder="Ville, Région"
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm hover:border-gray-300
                                          transition-all focus:outline-none focus:ring-2 focus:ring-[#1A3A6B]/20 focus:border-[#1A3A6B]">
                        </div>

                        {{-- Adresse --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Adresse de résidence</label>
                            <textarea name="address" rows="2"
                                      placeholder="Quartier, Ville, Rue..."
                                      class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm hover:border-gray-300 resize-none
                                             transition-all focus:outline-none focus:ring-2 focus:ring-[#1A3A6B]/20 focus:border-[#1A3A6B]">{{ old('address', $student->address) }}</textarea>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ── Section 2 : Parents & Tuteur ────────────────────────── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100"
                     style="background: linear-gradient(to right, #FEF3EA, #fff8f3);">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background-color:#E87722;">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold" style="color:#E87722;">Parents & Tuteur</h2>
                        <p class="text-xs text-gray-400">Contacts et personnes à prévenir</p>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        {{-- Père --}}
                        <div class="sm:col-span-2">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="w-1 h-4 rounded-full" style="background-color:#1A3A6B;"></span>
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Père</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Nom complet</label>
                                    <input type="text" name="father_name"
                                           value="{{ old('father_name', $student->father_name) }}"
                                           placeholder="Ex: NTANKEU Joseph"
                                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm hover:border-gray-300
                                                  transition-all focus:outline-none focus:ring-2 focus:ring-[#E87722]/20 focus:border-[#E87722]">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Téléphone</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-3 flex items-center">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                        </span>
                                        <input type="text" name="father_phone"
                                               value="{{ old('father_phone', $student->father_phone) }}"
                                               placeholder="+237 6XX XX XX XX"
                                               class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm hover:border-gray-300
                                                      transition-all focus:outline-none focus:ring-2 focus:ring-[#E87722]/20 focus:border-[#E87722]">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Mère --}}
                        <div class="sm:col-span-2">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="w-1 h-4 rounded-full" style="background-color:#BE185D;"></span>
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Mère</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Nom complet</label>
                                    <input type="text" name="mother_name"
                                           value="{{ old('mother_name', $student->mother_name) }}"
                                           placeholder="Ex: MEKOU Denise"
                                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm hover:border-gray-300
                                                  transition-all focus:outline-none focus:ring-2 focus:ring-[#E87722]/20 focus:border-[#E87722]">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Téléphone</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-3 flex items-center">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                        </span>
                                        <input type="text" name="mother_phone"
                                               value="{{ old('mother_phone', $student->mother_phone) }}"
                                               placeholder="+237 6XX XX XX XX"
                                               class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm hover:border-gray-300
                                                      transition-all focus:outline-none focus:ring-2 focus:ring-[#E87722]/20 focus:border-[#E87722]">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tuteur --}}
                        <div class="sm:col-span-2 border-t border-gray-100 pt-5">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="w-1 h-4 rounded-full bg-gray-400"></span>
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Tuteur / Contact d'urgence</span>
                                <span class="text-xs text-gray-400">(facultatif)</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Nom complet</label>
                                    <input type="text" name="guardian_name"
                                           value="{{ old('guardian_name', $student->guardian_name) }}"
                                           placeholder="Nom du tuteur"
                                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm hover:border-gray-300
                                                  transition-all focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Téléphone</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-3 flex items-center">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                        </span>
                                        <input type="text" name="guardian_phone"
                                               value="{{ old('guardian_phone', $student->guardian_phone) }}"
                                               placeholder="+237 6XX XX XX XX"
                                               class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm hover:border-gray-300
                                                      transition-all focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Lien avec l'élève</label>
                                    <input type="text" name="guardian_relationship"
                                           value="{{ old('guardian_relationship', $student->guardian_relationship) }}"
                                           placeholder="Ex: Oncle, Tante..."
                                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm hover:border-gray-300
                                                  transition-all focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ── Bouton Enregistrer (desktop) ────────────────────────── --}}
            <div class="hidden lg:flex items-center justify-between bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-4">
                <p class="text-sm text-gray-400">
                    <svg class="w-4 h-4 inline mr-1 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Les modifications s'appliquent immédiatement.
                </p>
                <div class="flex items-center gap-3">
                    <a href="{{ route('students.show', $student) }}"
                       class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                        Annuler
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-white text-sm font-bold shadow-sm transition-all hover:shadow-md active:scale-95"
                            style="background-color:#1A3A6B;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Enregistrer les modifications
                    </button>
                </div>
            </div>

        </div>
    </div>

</form>

<script>
function editStudentForm() {
    return {};
}
</script>

@if($student->photo)
<form id="delete-student-photo-form" method="POST" action="{{ route('students.photo.delete', $student) }}" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endif
@endsection
