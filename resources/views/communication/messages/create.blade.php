@extends('layouts.app')
@section('title', 'Nouveau message')
@section('page-title', 'Nouveau Message')
@section('page-subtitle', 'Envoyer un message à un ou plusieurs collègues')

@section('content')

<div class="max-w-2xl">
    <form method="POST" action="{{ route('communication.messages.store') }}">
        @csrf
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Destinataire(s) <span class="text-red-500">*</span>
                </label>
                <select name="recipient_ids[]" multiple
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm
                               focus:outline-none bg-white" style="min-height:120px;">
                    @foreach($staffUsers as $u)
                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">Maintenez Ctrl (ou Cmd) pour sélectionner plusieurs.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Sujet <span class="text-red-500">*</span>
                </label>
                <input type="text" name="subject"
                       class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Message <span class="text-red-500">*</span>
                </label>
                <textarea name="body" rows="6"
                          class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm
                                 focus:outline-none resize-none"></textarea>
            </div>
        </div>
        <div class="flex gap-3 mt-5">
            <a href="{{ route('communication.messages.index') }}"
               class="px-6 py-3 border border-gray-200 rounded-xl text-sm font-medium
                      text-gray-600 hover:bg-gray-50">Annuler</a>
            <button type="submit"
                    class="px-8 py-3 rounded-xl text-white text-sm font-bold hover:shadow-md"
                    style="background-color:#1A5C2A;">Envoyer</button>
        </div>
    </form>
</div>

@endsection