@extends('layouts.app')
@section('title', 'Messagerie')
@section('page-title', 'Messagerie')
@section('page-subtitle', 'Communication entre membres du personnel')

@section('content')

<div class="flex justify-between items-center mb-5">
    <div class="flex gap-2">
        @foreach(['inbox'=>'Reçus','sent'=>'Envoyés','archived'=>'Archivés'] as $f => $l)
        <a href="{{ route('communication.messages.index', ['folder'=>$f]) }}"
           class="px-4 py-2 rounded-xl text-sm font-bold transition-all"
           style="{{ $folder===$f ? 'background:#1A3A6B;color:#fff;' : 'background:white;color:#6B7280;border:1px solid #E5E7EB;' }}">
            {{ $l }}
            @if($f==='inbox' && $unreadCount > 0)
            <span class="ml-1 px-1.5 py-0.5 rounded-full text-xs bg-red-500 text-white">
                {{ $unreadCount }}
            </span>
            @endif
        </a>
        @endforeach
    </div>
    <a href="{{ route('communication.messages.create') }}"
       class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm
              font-bold hover:shadow-md transition-all" style="background-color:#1A5C2A;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        Nouveau message
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    @if($threads->isEmpty())
    <div class="px-5 py-12 text-center text-sm text-gray-400 italic">
        Aucun message dans ce dossier.
    </div>
    @else
    <div class="divide-y divide-gray-50">
        @foreach($threads as $t)
        @php
            $msg     = $folder === 'sent' ? $t : $t->message;
            $isUnread= $folder !== 'sent' && !$t->is_read;
            $other   = $folder === 'sent'
                ? $msg->recipients->pluck('recipient.name')->join(', ')
                : $msg->sender->name;
        @endphp
        <a href="{{ route('communication.messages.show', $msg) }}"
           class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition-colors
                  {{ $isUnread ? 'bg-blue-50/30' : '' }}">
            <div class="w-9 h-9 rounded-full flex items-center justify-center
                        text-white text-xs font-bold flex-shrink-0" style="background:#1A3A6B;">
                {{ strtoupper(substr($other,0,2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-sm {{ $isUnread ? 'font-black' : 'font-semibold' }} text-gray-800 truncate">
                        {{ $folder === 'sent' ? 'À : ' : 'De : ' }}{{ $other }}
                    </p>
                    <span class="text-xs text-gray-400 flex-shrink-0">
                        {{ $msg->created_at->diffForHumans() }}
                    </span>
                </div>
                <p class="text-sm {{ $isUnread ? 'font-bold text-gray-700' : 'text-gray-500' }} truncate">
                    {{ $msg->subject }}
                </p>
            </div>
            @if($isUnread)
            <span class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0"></span>
            @endif
        </a>
        @endforeach
    </div>
    @endif
</div>
{{ $threads->links() }}

@endsection