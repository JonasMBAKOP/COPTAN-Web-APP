@extends('layouts.app')
@section('title', 'Messages aux Parents')
@section('page-title', 'Communication avec les Parents')
@section('page-subtitle', 'SMS et WhatsApp')

@section('content')

@if($stats['simulation'])
<div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-5">
    <p class="text-sm font-bold text-amber-700">
        ⚠ Mode simulation actif — aucun message réel n'est envoyé. Configurez les clés
        API Twilio dans le fichier .env pour activer l'envoi réel.
    </p>
</div>
@endif

<div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-2xl font-black" style="color:#1A3A6B;">{{ $stats['total_sent'] }}</p>
        <p class="text-xs text-gray-400 uppercase tracking-wider mt-1">Messages envoyés</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-2xl font-black text-green-600">{{ $stats['this_month'] }}</p>
        <p class="text-xs text-gray-400 uppercase tracking-wider mt-1">Ce mois-ci</p>
    </div>
    <a href="{{ route('communication.parents.create') }}"
       class="rounded-2xl p-5 flex items-center justify-center gap-2 text-white
              font-bold text-sm hover:shadow-md transition-all"
       style="background-color:#E87722;">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        Nouveau message
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-black text-sm" style="color:#1A3A6B;">Envois récents</h3>
        <a href="{{ route('communication.parents.history') }}"
           class="text-xs font-bold hover:underline" style="color:#E87722;">Tout voir →</a>
    </div>
    @if($recentMessages->isEmpty())
    <div class="px-5 py-10 text-center text-sm text-gray-400 italic">Aucun message envoyé.</div>
    @else
    <div class="divide-y divide-gray-50">
        @foreach($recentMessages as $m)
        <a href="{{ route('communication.parents.show', $m) }}"
           class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition-colors">
            <div>
                <p class="text-sm font-bold text-gray-800">
                    {{ $m->subject ?: Str::limit($m->body, 50) }}
                </p>
                <p class="text-xs text-gray-400">
                    {{ $m->sender->name }} · {{ $m->created_at->diffForHumans() }}
                    · {{ ucfirst($m->channel) }}
                </p>
            </div>
            <div class="text-right">
                <span class="text-sm font-black text-green-600">{{ $m->sent_count }}</span>
                <span class="text-xs text-gray-400">/ {{ $m->total_recipients }}</span>
                <p class="text-xs px-2 py-0.5 rounded-full font-bold mt-1"
                   style="{{ $m->status==='completed' ? 'background:#D1FAE5;color:#065F46;' : 'background:#FEF3C7;color:#92400E;' }}">
                    {{ ucfirst($m->status) }}
                </p>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</div>

@endsection