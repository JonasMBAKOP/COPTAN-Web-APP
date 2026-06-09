@extends('layouts.app')

@section('title', $section->name)
@section('page-title', $section->name)
@section('page-subtitle', 'Détails et gestion de la section')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $section->name }}</h1>
            <p class="text-sm text-gray-600 mt-1">Code: <span class="font-mono">{{ $section->code }}</span></p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('sections.edit', $section) }}" 
               class="inline-flex items-center px-4 py-2 rounded-lg text-gray-700 font-semibold border border-gray-300 hover:bg-gray-50">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Éditer
            </a>
            <a href="{{ route('sections.index') }}" 
               class="inline-flex items-center px-4 py-2 rounded-lg text-gray-700 font-semibold border border-gray-300 hover:bg-gray-50">
                Retour
            </a>
        </div>
    </div>

    {{-- Informations principales --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-600">Nom</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $section->name }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-600">Code</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $section->code }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-600">Langue</p>
            <p class="text-2xl font-bold mt-2" style="color: {{ $section->isAnglophone() ? '#22C55E' : '#F59E0B' }};">
                {{ $section->language === 'en' ? 'English' : 'Français' }}
            </p>
        </div>
    </div>

    {{-- Niveaux associés --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Niveaux associés ({{ $section->levels->count() }})</h2>
        </div>
        @if($section->levels->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($section->levels as $level)
                    <div class="p-4 hover:bg-gray-50 flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $level->name }}</p>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $level->classGroups->count() }} classe(s)
                                @if($level->is_exam_class)
                                    <span class="ml-2 inline-block px-2 py-1 rounded text-xs font-medium" style="background-color: #FEE2E2; color: #DC2626;">
                                        Classe d'examen
                                    </span>
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('levels.show', $level) }}" 
                           class="text-blue-600 hover:text-blue-700 font-semibold">
                            Voir →
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-6 text-center text-gray-500">
                <p>Aucun niveau associé</p>
            </div>
        @endif
    </div>
</div>
@endsection
