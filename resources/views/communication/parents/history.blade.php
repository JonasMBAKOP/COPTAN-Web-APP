@extends('layouts.app')
@section('title', 'Historique')
@section('page-title', 'Historique des Envois')
@section('page-subtitle', 'Tous les messages envoyés aux parents')

@section('content')

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead>
            <tr style="background:#F8FAFC; border-bottom:1px solid #E5E7EB;">
                <th class="text-left px-5 py-3 text-xs font-bold text-gray-400 uppercase">Sujet</th>
                <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 uppercase">Cible</th>
                <th class="text-center px-4 py-3 text-xs font-bold text-gray-400 uppercase">Destinataires</th>
                <th class="text-center px-4 py-3 text-xs font-bold text-gray-400 uppercase">Statut</th>
                <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 uppercase">Date</th>
                <th class="text-right px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($messages as $m)
            <tr class="hover:bg-gray-50/50">
                <td class="px-5 py-3 text-sm font-semibold text-gray-800">
                    {{ $m->subject ?: Str::limit($m->body, 40) }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">
                    {{ $m->target_type === 'all' ? 'Tous' : ($m->target_type==='class' ? $m->classGroup?->full_name : 'Sélection') }}
                </td>
                <td class="px-4 py-3 text-center text-sm font-bold" style="color:#1A3A6B;">
                    {{ $m->sent_count }}/{{ $m->total_recipients }}
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold"
                          style="{{ $m->status==='completed' ? 'background:#D1FAE5;color:#065F46;' : 'background:#FEF3C7;color:#92400E;' }}">
                        {{ ucfirst($m->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-500">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                <td class="px-5 py-3 text-right">
                    <a href="{{ route('communication.parents.show', $m) }}"
                       class="text-xs font-bold hover:underline" style="color:#1A3A6B;">Voir →</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $messages->links() }}

@endsection