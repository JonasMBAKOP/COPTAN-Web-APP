@extends('layouts.app')

@section('title', 'Éditer Section')
@section('page-title', 'Modifier: ' . $section->name)
@section('page-subtitle', 'Mettez à jour les informations de la section')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('sections.update', $section) }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            {{-- Nom --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">
                    Nom <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                       value="{{ old('name', $section->name) }}"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Code --}}
            <div>
                <label for="code" class="block text-sm font-semibold text-gray-900 mb-2">
                    Code <span class="text-red-500">*</span>
                </label>
                <input type="text" name="code" id="code" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                       value="{{ old('code', $section->code) }}"
                       required>
                @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Langue --}}
            <div>
                <label for="language" class="block text-sm font-semibold text-gray-900 mb-2">
                    Langue <span class="text-red-500">*</span>
                </label>
                <select name="language" id="language" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        required>
                    <option value="fr" {{ old('language', $section->language) === 'fr' ? 'selected' : '' }}>Français</option>
                    <option value="en" {{ old('language', $section->language) === 'en' ? 'selected' : '' }}>English</option>
                </select>
                @error('language')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Boutons --}}
            <div class="flex gap-4 pt-6">
                <button type="submit" class="px-6 py-2 rounded-lg text-white font-semibold"
                        style="background-color: #E87722;">
                    Enregistrer les modifications
                </button>
                <a href="{{ route('sections.show', $section) }}" 
                   class="px-6 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
