@extends('layouts.app')
@section('title', 'Annonces')
@section('page-title', 'Annonces Internes')
@section('page-subtitle', 'Communications de la direction')

@section('content')

@can('manage-announcements')
<div class="flex justify-end mb-5">
    <a href="{{ route('communication.announcements.create') }}"
       class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-white
              text-sm font-bold transition-all hover:shadow-md"
       style="background-color:#E87722;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        Nouvelle annonce
    </a>
</div>
@endcan

@if($announcements->isEmpty())
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12
            text-center text-gray-400 text-sm">
    Aucune annonce publiée.
</div>
@else
<div class="space-y-4">
    @foreach($announcements as $ann)
    @php
        $catColors = [
            'pedagogique'   => ['bg'=>'#DBEAFE','text'=>'#1D4ED8'],
            'administratif' => ['bg'=>'#EDE9FE','text'=>'#6D28D9'],
            'financier'     => ['bg'=>'#D1FAE5','text'=>'#065F46'],
            'evenement'     => ['bg'=>'#FEF3C7','text'=>'#92400E'],
            'general'       => ['bg'=>'#F3F4F6','text'=>'#374151'],
        ];
        $cc = $catColors[$ann->category] ?? $catColors['general'];
    @endphp
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6
                {{ $ann->is_pinned ? 'border-l-4' : '' }}"
         style="{{ $ann->is_pinned ? 'border-left-color:#E87722;' : '' }}">
        <div class="flex items-start justify-between gap-3 mb-3">
            <div class="flex items-center gap-2 flex-wrap">
                @if($ann->is_pinned)
                <span class="text-xs">📌</span>
                @endif
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold"
                      style="background:{{ $cc['bg'] }};color:{{ $cc['text'] }};">
                    {{ $ann->category_label }}
                </span>
            </div>
            @if($ann->author_id === auth()->id() || auth()->user()->hasRole('super-admin'))
            <form method="POST" action="{{ route('communication.announcements.destroy', $ann) }}"
                  onsubmit="return confirm('Supprimer cette annonce ?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-gray-300 hover:text-red-500">✕</button>
            </form>
            @endif
        </div>
        <h3 class="font-black text-base mb-2" style="color:#1A3A6B;">{{ $ann->title }}</h3>
        <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $ann->content }}</p>
        <div class="flex items-center gap-2 mt-4 pt-3 border-t border-gray-100 text-xs text-gray-400">
            <span class="font-semibold text-gray-600">{{ $ann->author->name }}</span>
            <span>·</span>
            <span>{{ $ann->published_at->diffForHumans() }}</span>
        </div>
    </div>
    @endforeach
</div>
{{ $announcements->links() }}
@endif

@endsection