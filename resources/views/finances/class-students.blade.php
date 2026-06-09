@extends('layouts.app')

@section('title', 'Paiements — ' . $classGroup->full_name)
@section('page-title', 'Paiements de la classe')
@section('page-subtitle'){{ $classGroup->full_name }}
    — {{ $classGroup->academicYear->label }}@endsection

@section('breadcrumb')
    <a href="{{ route('finances.index') }}" class="hover:text-gray-700">
        Finances
    </a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round"
              stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span style="color:#1A3A6B;" class="font-medium">
        {{ $classGroup->full_name }}
    </span>
@endsection

@section('content')

{{-- ── EN-TÊTE CLASSE ───────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-5">
    <div class="flex flex-col sm:flex-row sm:items-center
                justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center
                        text-white font-black text-xl flex-shrink-0"
                 style="background-color:#1A3A6B;">
                {{ strtoupper(substr($classGroup->name, 0, 2)) }}
            </div>
            <div>
                <p class="font-black text-lg" style="color:#1A3A6B;">
                    {{ $classGroup->full_name }}
                </p>
                <p class="text-sm text-gray-500">
                    {{ $classGroup->level->section->name }}
                    · {{ $enrollments->count() }} élève(s)
                    · {{ $classGroup->academicYear->label }}
                </p>
            </div>
        </div>

        {{-- Stats rapides --}}
        <div class="flex items-center gap-4">
            <div class="text-center">
                <p class="text-xs text-gray-400">Attendu</p>
                <p class="font-bold text-sm" style="color:#1A3A6B;">
                    {{ number_format($totalDue) }}
                </p>
            </div>
            <div class="text-center border-l border-gray-200 pl-4">
                <p class="text-xs text-gray-400">Collecté</p>
                <p class="font-bold text-sm text-green-600">
                    {{ number_format($totalPaid) }}
                </p>
            </div>
            <div class="text-center border-l border-gray-200 pl-4">
                <p class="text-xs text-gray-400">Restant</p>
                <p class="font-bold text-sm text-red-500">
                    {{ number_format($totalRemaining) }}
                </p>
            </div>
            <div class="text-center border-l border-gray-200 pl-4">
                <p class="text-xs text-gray-400">Taux</p>
                <p class="font-bold text-sm
                           {{ $globalRate >= 80 ? 'text-green-600'
                               : ($globalRate >= 50 ? 'text-amber-600'
                               : 'text-red-500') }}">
                    {{ $globalRate }}%
                </p>
            </div>
        </div>
    </div>

    {{-- Barre de progression globale --}}
    <div class="mt-4">
        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all"
                 style="width:{{ $globalRate }}%;
                        background-color:{{ $globalRate >= 80
                            ? '#1A5C2A' : ($globalRate >= 50
                            ? '#C8A415' : '#EF4444') }}">
            </div>
        </div>
    </div>
</div>

{{-- ── ALERTE FRAIS NON CONFIGURÉS ─────────────────────────────────────── --}}
@if(!$feeStructure)
<div class="flex items-start gap-3 p-4 rounded-xl mb-5
            bg-amber-50 border border-amber-200">
    <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none"
         stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3
                 L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333
                 .192 3 1.732 3z"/>
    </svg>
    <div>
        <p class="text-sm font-semibold text-amber-800">
            Frais non configurés
        </p>
        <p class="text-xs text-amber-700 mt-0.5">
            Configurez les frais avant d'enregistrer des paiements.
        </p>
        @can('configure-fees')
        <a href="{{ route('finances.fees', $classGroup) }}"
           class="inline-block mt-1 text-xs font-medium hover:underline"
           style="color:#E87722;">
            → Configurer maintenant
        </a>
        @endcan
    </div>
</div>
@endif

{{-- ── TABLEAU DES ÉLÈVES ───────────────────────────────────────────────── --}}
@if($enrollments->isEmpty())
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12
            text-center text-gray-400">
    <p class="text-sm">Aucun élève inscrit dans cette classe.</p>
</div>
@else

{{-- Desktop --}}
<div class="hidden md:block bg-white rounded-2xl shadow-sm
            border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead>
            <tr style="background-color:#F8FAFC;"
                class="border-b border-gray-100">
                <th class="text-left px-5 py-3.5 text-xs font-semibold
                           text-gray-400 uppercase tracking-wider">
                    Élève
                </th>
                @if($feeStructure)
                @foreach($feeStructure->installments->sortBy('installment_number')
                    as $inst)
                <th class="text-center px-3 py-3.5 text-xs font-semibold
                           text-gray-400 uppercase tracking-wider">
                    {{ $inst->label }}
                    <span class="block font-normal text-gray-300 normal-case">
                        {{ number_format($inst->amount) }} F
                    </span>
                </th>
                @endforeach
                @endif
                <th class="text-right px-4 py-3.5 text-xs font-semibold
                           text-gray-400 uppercase tracking-wider">
                    Total payé
                </th>
                <th class="text-center px-4 py-3.5 text-xs font-semibold
                           text-gray-400 uppercase tracking-wider">
                    Statut
                </th>
                <th class="text-right px-5 py-3.5 text-xs font-semibold
                           text-gray-400 uppercase tracking-wider">
                    Action
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($enrollments as $row)
            @php
                $enr = $row['enrollment'];
                $statusConf = [
                    'paid'    => ['bg' => '#D1FAE5', 'text' => '#065F46',
                                  'label' => 'Soldé'],
                    'partial' => ['bg' => '#FEF3C7', 'text' => '#92400E',
                                  'label' => 'Partiel'],
                    'unpaid'  => ['bg' => '#FEE2E2', 'text' => '#991B1B',
                                  'label' => 'Impayé'],
                ];
                $sc = $statusConf[$row['status']];
            @endphp
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        @if($enr->student->photo)
                        <img src="{{ $enr->student->photo_url }}"
                             class="w-8 h-8 rounded-full object-cover
                                    flex-shrink-0">
                        @else
                        <div class="w-8 h-8 rounded-full flex items-center
                                    justify-center text-white text-xs font-bold
                                    flex-shrink-0"
                             style="background-color:
                                {{ $enr->student->gender === 'M'
                                    ? '#1D4ED8' : '#BE185D' }};">
                            {{ strtoupper(substr($enr->student->last_name, 0, 1))
                               . strtoupper(substr($enr->student->first_name, 0, 1)) }}
                        </div>
                        @endif
                        <div>
                            <p class="text-sm font-medium text-gray-800">
                                {{ $enr->student->full_name }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $enr->student->matricule }}
                            </p>
                        </div>
                    </div>
                </td>

                @if($feeStructure)
                @foreach($feeStructure->installments->sortBy('installment_number')
                    as $inst)
                @php
                    $instPaid = \App\Models\StudentPayment::where([
                        'student_enrollment_id' => $enr->id,
                        'fee_installment_id'    => $inst->id,
                    ])->sum('amount_paid');
                    $instStatus = $instPaid <= 0 ? 'unpaid'
                        : ($instPaid >= $inst->amount ? 'paid' : 'partial');
                    $icons = ['paid' => '✓', 'partial' => '◑', 'unpaid' => '—'];
                    $colors = ['paid' => '#1A5C2A', 'partial' => '#C8A415',
                               'unpaid' => '#D1D5DB'];
                @endphp
                <td class="px-3 py-3.5 text-center">
                    <div class="flex flex-col items-center gap-0.5">
                        <span class="text-sm font-bold"
                              style="color:{{ $colors[$instStatus] }};">
                            {{ $icons[$instStatus] }}
                        </span>
                        @if($instPaid > 0)
                        <span class="text-xs text-gray-500">
                            {{ number_format($instPaid) }}
                        </span>
                        @endif
                    </div>
                </td>
                @endforeach
                @endif

                <td class="px-4 py-3.5 text-right">
                    <p class="text-sm font-semibold text-green-600">
                        {{ number_format($row['paid']) }}
                    </p>
                    @if($row['remaining'] > 0)
                    <p class="text-xs text-red-500">
                        -{{ number_format($row['remaining']) }}
                    </p>
                    @endif
                </td>

                <td class="px-4 py-3.5 text-center">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium"
                          style="background-color:{{ $sc['bg'] }};
                                 color:{{ $sc['text'] }};">
                        {{ $sc['label'] }}
                    </span>
                </td>

                <td class="px-5 py-3.5 text-right">
                    <a href="{{ route('finances.student', $enr) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5
                              rounded-lg text-white text-xs font-medium
                              transition-all hover:shadow-md"
                       style="background-color:
                           {{ $row['status'] === 'paid'
                               ? '#6B7280' : '#1A5C2A' }};">
                        @if($row['status'] === 'paid')
                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Voir
                        @else
                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M12 4v16m8-8H4"/>
                        </svg>
                        Payer
                        @endif
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Mobile --}}
<div class="md:hidden space-y-3">
    @foreach($enrollments as $row)
    @php
        $enr = $row['enrollment'];
        $sc  = [
            'paid'    => ['bg' => '#D1FAE5', 'text' => '#065F46', 'label' => 'Soldé'],
            'partial' => ['bg' => '#FEF3C7', 'text' => '#92400E', 'label' => 'Partiel'],
            'unpaid'  => ['bg' => '#FEE2E2', 'text' => '#991B1B', 'label' => 'Impayé'],
        ][$row['status']];
    @endphp
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between gap-3 mb-3">
            <div class="flex items-center gap-3 min-w-0">
                @if($enr->student->photo)
                <img src="{{ $enr->student->photo_url }}"
                     class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                @else
                <div class="w-9 h-9 rounded-full flex items-center justify-center
                            text-white text-xs font-bold flex-shrink-0"
                     style="background-color:#1A3A6B;">
                    {{ strtoupper(substr($enr->student->last_name, 0, 1))
                       . strtoupper(substr($enr->student->first_name, 0, 1)) }}
                </div>
                @endif
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">
                        {{ $enr->student->full_name }}
                    </p>
                    <p class="text-xs text-gray-400">
                        {{ $enr->student->matricule }}
                    </p>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                         flex-shrink-0"
                  style="background-color:{{ $sc['bg'] }};
                         color:{{ $sc['text'] }};">
                {{ $sc['label'] }}
            </span>
        </div>

        <div class="flex items-center justify-between">
            <div class="text-sm">
                <span class="text-gray-400 text-xs">Payé : </span>
                <span class="font-semibold text-green-600">
                    {{ number_format($row['paid']) }} FCFA
                </span>
                @if($row['remaining'] > 0)
                · <span class="text-red-500 text-xs">
                    -{{ number_format($row['remaining']) }}
                </span>
                @endif
            </div>
            <a href="{{ route('finances.student', $enr) }}"
               class="flex items-center gap-1 px-3 py-1.5 rounded-lg
                      text-white text-xs font-medium"
               style="background-color:
                   {{ $row['status'] === 'paid' ? '#6B7280' : '#1A5C2A' }};">
                {{ $row['status'] === 'paid' ? 'Voir' : '+ Payer' }}
            </a>
        </div>
    </div>
    @endforeach
</div>

@endif

@endsection