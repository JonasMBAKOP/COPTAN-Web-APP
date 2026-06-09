@extends('layouts.app')

@section('title', 'Paiements')
@section('page-title', 'Tous les Paiements')
@section('page-subtitle', 'Historique complet des paiements')

@section('content')

{{-- ── FILTRES ───────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-5">
    <form method="GET" action="{{ route('finances.payments') }}"
          class="flex flex-wrap gap-3">

        <select name="year_id" onchange="this.form.submit()"
                class="px-3 py-2 border border-gray-200 rounded-lg text-sm
                       focus:outline-none bg-white">
            <option value="">Toutes les années</option>
            @foreach(\App\Models\AcademicYear::orderByDesc('start_date')
                ->get() as $yr)
            <option value="{{ $yr->id }}"
                    {{ $selectedYearId == $yr->id ? 'selected' : '' }}>
                {{ $yr->label }}
            </option>
            @endforeach
        </select>

        <select name="class_id" onchange="this.form.submit()"
                class="px-3 py-2 border border-gray-200 rounded-lg text-sm
                       focus:outline-none bg-white">
            <option value="">Toutes les classes</option>
            @foreach($classes as $cls)
            <option value="{{ $cls->id }}"
                    {{ request('class_id') == $cls->id ? 'selected' : '' }}>
                {{ $cls->full_name }}
            </option>
            @endforeach
        </select>

        <select name="method" onchange="this.form.submit()"
                class="px-3 py-2 border border-gray-200 rounded-lg text-sm
                       focus:outline-none bg-white">
            <option value="">Tous les modes</option>
            @foreach([
                'cash'          => 'Espèces',
                'orange_money'  => 'Orange Money',
                'mtn_momo'      => 'MTN MoMo',
                'bank_transfer' => 'Virement',
            ] as $val => $lbl)
            <option value="{{ $val }}"
                    {{ request('method') === $val ? 'selected' : '' }}>
                {{ $lbl }}
            </option>
            @endforeach
        </select>

        <div class="relative flex-1 min-w-40">
            <input type="text" name="search"
                   value="{{ request('search') }}"
                   placeholder="Nom, matricule, n° reçu..."
                   class="w-full pl-9 pr-4 py-2 border border-gray-200
                          rounded-lg text-sm focus:outline-none">
            <span class="absolute inset-y-0 left-3 flex items-center
                         text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </span>
        </div>

        @if(request()->hasAny(['year_id','class_id','method','search']))
        <a href="{{ route('finances.payments') }}"
           class="px-3 py-2 border border-gray-200 rounded-lg text-sm
                  text-gray-500 hover:bg-gray-50">✕</a>
        @endif
    </form>
</div>

{{-- Total filtré --}}
<div class="flex items-center justify-between mb-4">
    <p class="text-sm text-gray-500">
        <strong class="text-gray-800">{{ $payments->total() }}</strong>
        paiement(s)
    </p>
    <p class="text-sm font-semibold text-green-600">
        Total : {{ number_format($payments->sum('amount_paid')) }} FCFA
    </p>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100
            overflow-hidden">
    @if($payments->isEmpty())
    <div class="p-12 text-center text-gray-400">
        <p class="text-sm">Aucun paiement trouvé.</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="background-color:#F8FAFC;"
                    class="border-b border-gray-100">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold
                               text-gray-400 uppercase tracking-wider">
                        Élève
                    </th>
                    <th class="text-left px-4 py-3.5 text-xs font-semibold
                               text-gray-400 uppercase tracking-wider
                               hidden sm:table-cell">
                        Tranche
                    </th>
                    <th class="text-right px-4 py-3.5 text-xs font-semibold
                               text-gray-400 uppercase tracking-wider">
                        Montant
                    </th>
                    <th class="text-left px-4 py-3.5 text-xs font-semibold
                               text-gray-400 uppercase tracking-wider
                               hidden md:table-cell">
                        Mode
                    </th>
                    <th class="text-left px-4 py-3.5 text-xs font-semibold
                               text-gray-400 uppercase tracking-wider
                               hidden lg:table-cell">
                        Date
                    </th>
                    <th class="text-left px-4 py-3.5 text-xs font-semibold
                               text-gray-400 uppercase tracking-wider
                               hidden lg:table-cell">
                        N° Reçu
                    </th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold
                               text-gray-400 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($payments as $p)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3.5">
                        <a href="{{ route('finances.student',
                                         $p->studentEnrollment) }}"
                           class="hover:underline">
                            <p class="text-sm font-medium text-gray-800">
                                {{ $p->studentEnrollment?->student?->full_name }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $p->studentEnrollment?->classGroup?->full_name }}
                            </p>
                        </a>
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600
                               hidden sm:table-cell">
                        {{ $p->feeInstallment?->label }}
                    </td>
                    <td class="px-4 py-3.5 text-right">
                        <span class="font-semibold text-green-600">
                            {{ number_format($p->amount_paid) }}
                        </span>
                        <span class="text-xs text-gray-400 ml-0.5">FCFA</span>
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600
                               hidden md:table-cell">
                        {{ $p->payment_method_label }}
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600
                               hidden lg:table-cell">
                        {{ $p->payment_date->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-3.5 hidden lg:table-cell">
                        <span class="font-mono text-xs"
                              style="color:#1A3A6B;">
                            {{ $p->receipt_number }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <a href="{{ route('finances.receipt', $p) }}"
                           target="_blank"
                           class="p-1.5 rounded-lg text-gray-400
                                  hover:text-blue-600 hover:bg-blue-50
                                  transition-colors inline-flex"
                           title="Voir le reçu">
                            <svg class="w-4 h-4" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2
                                         2 0 012-2h5.586a1 1 0 01.707.293l5.414
                                         5.414a1 1 0 01.293.707V19a2 2 0 01-2
                                         2z"/>
                            </svg>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($payments->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">
        {{ $payments->links() }}
    </div>
    @endif
    @endif
</div>

@endsection