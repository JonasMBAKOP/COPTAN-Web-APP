@extends('layouts.app')
@section('title', 'Nouvelle annonce')
@section('page-title', 'Nouvelle Annonce')
@section('page-subtitle', 'Diffuser un message à l\'ensemble du personnel')

@section('content')

<div class="max-w-2xl">
    <form method="POST" action="{{ route('communication.announcements.store') }}">
        @csrf
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Titre <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}"
                       class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm
                              focus:outline-none @error('title') border-red-400 @enderror">
                @error('title')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Catégorie <span class="text-red-500">*</span>
                </label>
                <select name="category" class="w-full px-3 py-2.5 border border-gray-200
                                                  rounded-xl text-sm focus:outline-none bg-white">
                    <option value="general">Général</option>
                    <option value="pedagogique">Pédagogique</option>
                    <option value="administratif">Administratif</option>
                    <option value="financier">Financier</option>
                    <option value="evenement">Événement</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Contenu <span class="text-red-500">*</span>
                </label>
                <textarea name="content" rows="6"
                          class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm
                                 focus:outline-none resize-none
                                 @error('content') border-red-400 @enderror">{{ old('content') }}</textarea>
                @error('content')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_pinned" value="1"
                       class="w-4 h-4 rounded" style="accent-color:#E87722;">
                <span class="text-sm font-medium text-gray-700">
                    Épingler cette annonce en haut de la liste
                </span>
            </label>
        </div>

        <div class="flex gap-3 mt-5">
            <a href="{{ route('communication.announcements.index') }}"
               class="flex-1 sm:flex-none px-6 py-3 border border-gray-200 rounded-xl
                      text-sm font-medium text-gray-600 text-center hover:bg-gray-50">
                Annuler
            </a>
            <button type="submit"
                    class="flex-1 sm:flex-none px-8 py-3 rounded-xl text-white text-sm
                           font-bold hover:shadow-md transition-all"
                    style="background-color:#E87722;">
                Publier l'annonce
            </button>
        </div>
    </form>
</div>

@endsection