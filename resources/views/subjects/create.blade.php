@extends('layouts.app')

@section('title', 'Nouvelle matière')
@section('page-title', 'Nouvelle Matière')
@section('page-subtitle', 'Ajouter au catalogue')

@section('breadcrumb')
    <a href="{{ route('subjects.index') }}" class="hover:text-gray-700">
        Matières
    </a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round"
              stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="font-medium" style="color: #1A3A6B;">Nouvelle matière</span>
@endsection

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('subjects.store') }}">
        @csrf

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6
                    space-y-5">
            <h3 class="text-sm font-semibold uppercase tracking-wider
                       text-gray-400 pb-2 border-b border-gray-100">
                Informations de la matière
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Code --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code"
                           value="{{ old('code') }}"
                           placeholder="Ex: MATH, FRAN, ANG"
                           maxlength="20"
                           class="w-full px-3 py-2.5 border rounded-lg text-sm
                                  font-mono font-semibold uppercase
                                  focus:outline-none focus:ring-2
                                  focus:ring-blue-200
                                  @error('code') border-red-400
                                  @else border-gray-200 @enderror"
                           style="color: #1A3A6B;"
                           oninput="this.value = this.value.toUpperCase()">
                    @error('code')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Catégorie --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Catégorie <span class="text-red-500">*</span>
                    </label>
                    <select name="subject_category_id"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm
                                   focus:outline-none focus:ring-2
                                   focus:ring-blue-200 bg-white
                                   @error('subject_category_id') border-red-400
                                   @else border-gray-200 @enderror">
                        <option value="">Sélectionner...</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                                {{ old('subject_category_id') == $cat->id
                                    ? 'selected' : '' }}>
                            {{ $cat->name_fr }}
                        </option>
                        @endforeach
                    </select>
                    @error('subject_category_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nom FR --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nom (Français) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name_fr"
                           value="{{ old('name_fr') }}"
                           placeholder="Ex: Mathématiques"
                           class="w-full px-3 py-2.5 border rounded-lg text-sm
                                  focus:outline-none focus:ring-2
                                  focus:ring-blue-200
                                  @error('name_fr') border-red-400
                                  @else border-gray-200 @enderror">
                    @error('name_fr')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nom EN --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nom (Anglais)
                        <span class="text-xs text-gray-400 font-normal">
                            (optionnel)
                        </span>
                    </label>
                    <input type="text" name="name_en"
                           value="{{ old('name_en') }}"
                           placeholder="Ex: Mathematics"
                           class="w-full px-3 py-2.5 border border-gray-200
                                  rounded-lg text-sm focus:outline-none
                                  focus:ring-2 focus:ring-blue-200">
                </div>

                {{-- Type --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Type <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                        @foreach([
                            ['val' => 'general',   'label' => 'Générale',
                             'color' => 'blue'],
                            ['val' => 'technical', 'label' => 'Technique',
                             'color' => 'purple'],
                            // ['val' => 'language',  'label' => 'Langue',
                            //  'color' => 'yellow'],
                            // ['val' => 'sport',     'label' => 'Sport',
                            //  'color' => 'green'],
                            ['val' => 'other',     'label' => 'Autre',
                             'color' => 'gray'],
                        ] as $t)
                        <label class="flex items-center gap-2 p-3 border rounded-lg
                                      cursor-pointer transition-colors
                                      hover:bg-gray-50 text-sm
                                      {{ old('type') === $t['val']
                                          ? 'border-blue-400 bg-blue-50'
                                          : 'border-gray-200' }}">
                            <input type="radio" name="type"
                                   value="{{ $t['val'] }}"
                                   {{ old('type') === $t['val']
                                       ? 'checked' : '' }}
                                   class="flex-shrink-0"
                                   style="accent-color: #1A3A6B;">
                            {{ $t['label'] }}
                        </label>
                        @endforeach
                    </div>
                    @error('type')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        <div class="flex gap-3 mt-4">
            <a href="{{ route('subjects.index') }}"
               class="flex-1 py-2.5 border border-gray-200 rounded-xl
                      text-sm font-medium text-gray-600 text-center
                      hover:bg-gray-50">
                Annuler
            </a>
            <button type="submit"
                    class="flex-2 px-8 py-2.5 rounded-xl text-white
                           text-sm font-semibold flex items-center
                           justify-center gap-2"
                    style="background-color: #1A5C2A;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Créer la matière
            </button>
        </div>
    </form>
</div>
@endsection