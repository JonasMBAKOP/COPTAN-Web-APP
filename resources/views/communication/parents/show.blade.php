@extends('layouts.app')
@section('title', 'Détail envoi')
@section('page-title', 'Détail de l\'envoi')
@section('page-subtitle'){{ $parentMessage->subject ?: 'Message aux parents' }}@endsection

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wider">Destinataires</p>
        <p class="text-2xl font-black" style="color:#1A3A6B;">{{ $parentMessage->total_recipients }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wider">Envoyés</p>
        <p class="text-2xl font-black text-green-600">{{ $parentMessage->sent_count }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wider">Échoués</p>
        <p class="text-2xl font-black text-red-500">{{ $parentMessage->failed_count }}</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $parentMessage->body }}</p>
    <p class="text-xs text-gray-400 mt-3">
        Envoyé par {{ $parentMessage->sender->name }} · {{ $parentMessage->created_at->format('d/m/Y H:i') }}
        · Canal : {{ ucfirst($parentMessage->channel) }}
    </p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-black text-sm" style="color:#1A3A6B;">Détail des destinataires</h3>
    </div>
    <table class="w-full">
        <thead>
            <tr style="background:#F8FAFC; border-bottom:1px solid #E5E7EB;">
                <th class="text-left px-5 py-3 text-xs font-bold text-gray-400 uppercase">Élève</th>
                <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 uppercase">Téléphone</th>
                <th class="text-center px-4 py-3 text-xs font-bold text-gray-400 uppercase">SMS</th>
                <th class="text-center px-4 py-3 text-xs font-bold text-gray-400 uppercase">WhatsApp</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($parentMessage->recipients as $r)
            <tr class="hover:bg-gray-50/50">
                <td class="px-5 py-3 text-sm font-semibold text-gray-800">
                    {{ $r->student->full_name }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ $r->phone_number }}</td>
                <td class="px-4 py-3 text-center">
                    @php $sc=['sent'=>['D1FAE5','065F46','✓'],'failed'=>['FEE2E2','991B1B','✗'],'pending'=>['FEF3C7','92400E','…'],'skipped'=>['F3F4F6','9CA3AF','—']][$r->sms_status]??null; @endphp
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold"
                          style="background:#{{ $sc[0] }};color:#{{ $sc[1] }};">{{ $sc[2] }}</span>
                </td>
                <td class="px-4 py-3 text-center">
                    @php $wc=['sent'=>['D1FAE5','065F46','✓'],'failed'=>['FEE2E2','991B1B','✗'],'pending'=>['FEF3C7','92400E','…'],'skipped'=>['F3F4F6','9CA3AF','—']][$r->whatsapp_status]??null; @endphp
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold"
                          style="background:#{{ $wc[0] }};color:#{{ $wc[1] }};">{{ $wc[2] }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection