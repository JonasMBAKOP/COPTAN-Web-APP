@extends('layouts.app')

@section('title', 'Éditer Niveau')
@section('page-title', 'Modifier: ' . $level->name)
@section('page-subtitle', 'Mettez à jour les informations du niveau')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('levels.update', $level) }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            {{-- Section --}}
            <div>
                <label for="section_id" class="block text-sm font-semibold text-gray-900 mb-2">
                    Section <span class="text-red-500">*</span>
                </label>
                <select name="section_id" id="section_id" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        required>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" {{ old('section_id', $level->section_id) === (string)$section->id ? 'selected' : '' }}>
                            {{ $section->name }}
                        </option>
                    @endforeach
                </select>
                @error('section_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nom --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">
                    Nom <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                       value="{{ old('name', $level->name) }}"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Ordre d'affichage --}}
            <div>
                <label for="order_index" class="block text-sm font-semibold text-gray-900 mb-2">
                    Ordre d'affichage <span class="text-red-500">*</span>
                </label>
                <input type="number" name="order_index" id="order_index" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                       value="{{ old('order_index', $level->order_index) }}" min="1" max="12"
                       required>
                @error('order_index')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Classe d'examen --}}
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_exam_class" value="1" 
                           class="w-4 h-4 rounded" {{ old('is_exam_class', $level->is_exam_class) ? 'checked' : '' }}>
                    <span class="ml-2 text-sm font-semibold text-gray-900">C'est une classe d'examen</span>
                </label>
                @error('is_exam_class')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Boutons --}}
            <div class="flex gap-4 pt-6">
                <button type="submit" class="px-6 py-2 rounded-lg text-white font-semibold"
                        style="background-color: #E87722;">
                    Enregistrer les modifications
                </button>
                <a href="{{ route('levels.show', $level) }}" 
                   class="px-6 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
