@extends('layouts.app')
@section('title', $message->subject)
@section('page-title', 'Message')
@section('page-subtitle'){{ $message->subject }}@endsection

@section('content')

<div class="max-w-2xl bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center
                        text-white font-bold" style="background:#1A3A6B;">
                {{ strtoupper(substr($message->sender->name,0,2)) }}
            </div>
            <div>
                <p class="text-sm font-bold text-gray-800">{{ $message->sender->name }}</p>
                <p class="text-xs text-gray-400">
                    À : {{ $message->recipients->pluck('recipient.name')->join(', ') }}
                </p>
            </div>
        </div>
        <span class="text-xs text-gray-400">{{ $message->created_at->format('d/m/Y H:i') }}</span>
    </div>
    <h3 class="font-black text-lg mb-3" style="color:#1A3A6B;">{{ $message->subject }}</h3>
    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $message->body }}</p>
</div>

<a href="{{ route('communication.messages.index') }}"
   class="inline-block mt-4 text-sm font-medium text-gray-600 hover:underline">
    ← Retour à la messagerie
</a>

@endsection