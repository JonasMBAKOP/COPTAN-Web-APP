@extends('layouts.app')

@section('title', $level->name)
@section('page-title', $level->name)
@section('page-subtitle', 'Détails et gestion du niveau')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $level->name }}</h1>
            <p class="text-sm text-gray-600 mt-1">Section: <span class="font-semibold">{{ $level->section->name }}</span></p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('levels.edit', $level) }}" 
               class="inline-flex items-center px-4 py-2 rounded-lg text-gray-700 font-semibold border border-gray-300 hover:bg-gray-50">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Éditer
            </a>
            <a href="{{ route('levels.index') }}" 
               class="inline-flex items-center px-4 py-2 rounded-lg text-gray-700 font-semibold border border-gray-300 hover:bg-gray-50">
                Retour
            </a>
        </div>
    </div>

    {{-- Informations principales --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-600">Ordre d'affichage</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $level->order_index }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-600">Type</p>
            <p class="text-lg font-bold mt-2">
                @if($level->is_exam_class)
                    <span style="color: #DC2626;">Classe d'examen</span>
                @else
                    <span style="color: #22C55E;">Classe normale</span>
                @endif
            </p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-600">Nombre de classes</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $level->classGroups->count() }}</p>
        </div>
    </div>

    {{-- Classes associées --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Classes associées ({{ $level->classGroups->count() }})</h2>
        </div>
        @if($level->classGroups->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($level->classGroups as $classGroup)
                    <div class="p-4 hover:bg-gray-50 flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $classGroup->name }}</p>
                            <p class="text-sm text-gray-600 mt-1">
                                Effectif: <span class="font-semibold">{{ $classGroup->studentEnrollments->count() }}/{{ $classGroup->max_students }}</span>
                            </p>
                        </div>
                        <a href="{{ route('class-groups.show', $classGroup) }}" 
                           class="text-blue-600 hover:text-blue-700 font-semibold">
                            Voir →
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-6 text-center text-gray-500">
                <p>Aucune classe associée</p>
            </div>
        @endif
    </div>
</div>
@endsection
