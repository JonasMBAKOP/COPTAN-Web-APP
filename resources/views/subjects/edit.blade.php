@extends('layouts.app')

@section('title', 'Modifier ' . $subject->name_fr)
@section('page-title', 'Modifier la matière')
@section('page-subtitle'){{ $subject->name_fr }}@endsection

@section('breadcrumb')
    <a href="{{ route('subjects.index') }}" class="hover:text-gray-700">
        Matières
    </a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round"
              stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="font-medium" style="color: #1A3A6B;">
        {{ $subject->name_fr }}
    </span>
@endsection

@section('content')
<div class="w-full px-4 lg:px-8">
    <form method="POST" action="{{ route('subjects.update', $subject) }}">
        @csrf @method('PUT')

        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8
                space-y-6 max-w-full">
            <h3 class="text-sm font-semibold uppercase tracking-wider
                       text-gray-400 pb-2 border-b border-gray-100">
                Informations de la matière
            </h3>

            <div class="grid grid-cols-1 gap-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nom de la matière <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name_fr"
                               value="{{ old('name_fr', $subject->name_fr) }}"
                               class="w-full px-4 py-3 border rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200 {{ $errors->has('name_fr') ? 'border-red-400' : 'border-gray-200' }}">
                        @error('name_fr')
                        <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Catégorie <span class="text-red-500">*</span>
                        </label>
                        <select name="subject_category_id"
                                class="w-full px-4 py-3 border rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white {{ $errors->has('subject_category_id') ? 'border-red-400' : 'border-gray-200' }}">
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                    {{ old('subject_category_id', $subject->subject_category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name_fr }}
                            </option>
                            @endforeach
                        </select>
                        @error('subject_category_id')
                        <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div class="rounded-3xl border border-gray-100 bg-slate-50 p-6">
                        <div class="flex items-center justify-between gap-4 mb-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-indigo-500">
                                    Configuration rapide
                                </p>
                                <h3 class="text-base font-semibold text-slate-900">
                                    Type de matière
                                </h3>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            @foreach([
                                ['val' => 'general',   'label' => 'Générale'],
                                ['val' => 'technical', 'label' => 'Technique'],
                                // ['val' => 'language',  'label' => 'Langue'],
                                // ['val' => 'sport',     'label' => 'Sport'],
                                ['val' => 'other',     'label' => 'Autre'],
                            ] as $t)
                            @php
                                $checked = old('type', $subject->type) === $t['val'];
                            @endphp
                            <label class="flex items-center gap-3 p-3 rounded-2xl border transition-colors cursor-pointer text-sm
                                          {{ $checked ? 'border-indigo-300 bg-indigo-50' : 'border-gray-200 bg-white hover:border-indigo-200' }}">
                                <input type="radio" name="type"
                                       value="{{ $t['val'] }}"
                                       {{ $checked ? 'checked' : '' }}
                                       class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                <span class="font-medium text-slate-700">{{ $t['label'] }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-3 mt-6 justify-end">
            <a href="{{ route('subjects.index') }}"
               class="px-6 py-2.5 border border-gray-200 rounded-full
                      text-sm font-medium text-gray-600 hover:bg-gray-50">
                Annuler
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-full text-white text-sm font-semibold flex items-center justify-center gap-2"
                    style="background-color: #0F4670;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Enregistrer
            </button>
        </div>
    </form>
</div>
@endsection