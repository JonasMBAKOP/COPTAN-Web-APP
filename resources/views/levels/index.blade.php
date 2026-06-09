@extends('layouts.app')

@section('title', 'Niveaux')
@section('page-title', 'Gestion des Niveaux')
@section('page-subtitle', 'Organisez vos classes par niveaux d\'études')

@section('content')
<div class="space-y-6">
    {{-- Header avec bouton créer --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Niveaux</h1>
            <p class="text-sm text-gray-600 mt-1">Gérez les niveaux par section (6ème, 5ème, etc.)</p>
        </div>
        <a href="{{ route('levels.create') }}" 
           class="inline-flex items-center px-4 py-2 rounded-lg text-white font-semibold"
           style="background-color: #E87722;">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau Niveau
        </a>
    </div>

    {{-- Messages --}}
    @if ($message = Session::get('success'))
        <div class="p-4 rounded-lg border border-green-200" style="background-color: #D4EDDA;">
            <p class="text-green-700">{{ $message }}</p>
        </div>
    @endif

    {{-- Filtrer par section --}}
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('levels.index') }}" 
           class="px-4 py-2 rounded-lg font-semibold {{ !request('section') ? 'text-white' : 'text-gray-700 border border-gray-300 hover:bg-gray-50' }}"
           style="background-color: {{ !request('section') ? '#1A3A6B' : 'transparent' }};">
            Tous
        </a>
        @foreach($sections as $section)
            <a href="{{ route('levels.index', ['section' => $section->id]) }}" 
               class="px-4 py-2 rounded-lg font-semibold {{ request('section') == $section->id ? 'text-white' : 'text-gray-700 border border-gray-300 hover:bg-gray-50' }}"
               style="background-color: {{ request('section') == $section->id ? '#1A3A6B' : 'transparent' }};">
                {{ $section->name }}
            </a>
        @endforeach
    </div>

    {{-- Tableau des niveaux --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @if($levels->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Ordre</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nom</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Section</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Classes</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($levels as $level)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-blue-50" style="color: #1A3A6B;">
                                        {{ $level->order_index }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-gray-900">{{ $level->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-gray-600">{{ $level->section->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($level->is_exam_class)
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium" style="background-color: #FEE2E2; color: #DC2626;">
                                            Examen
                                        </span>
                                    @else
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                            Normal
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="font-semibold">{{ $level->classGroups->count() }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('levels.show', $level) }}" 
                                           class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('levels.edit', $level) }}" 
                                           class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('levels.destroy', $level) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Êtes-vous certain ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $levels->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <p class="text-gray-500">Aucun niveau trouvé</p>
            </div>
        @endif
    </div>
</div>
@endsection
