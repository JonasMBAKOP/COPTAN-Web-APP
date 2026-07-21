@extends('layouts.app')

@section('title', 'Salaires')
@section('page-title', 'Salaires du personnel')
@section('page-subtitle', 'Liste des membres du personnel par type de contrat')

@section('content')
<div>
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Salaires</h2>
            <p class="text-sm text-gray-500 mt-1">Consultez les fiches par type de contrat.</p>
        </div>

        <form method="GET" action="{{ route('staff.salaries') }}"
              class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <select name="contract" onchange="this.form.submit()"
                        class="appearance-none pl-3 pr-8 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent min-w-[180px] cursor-pointer">
                    <option value="">Tous les contrats</option>
                    @foreach(\App\Models\Staff::contractLabels() as $value => $label)
                        <option value="{{ $value }}" {{ request('contract') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <span class="pointer-events-none absolute inset-y-0 right-2.5 flex items-center text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </span>
            </div>

            <div class="relative w-full sm:w-auto">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Rechercher un membre"
                       class="pl-3 pr-10 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent min-w-[220px]">
                @if(request('search'))
                    <a href="{{ route('staff.salaries', array_merge(request()->except('search', 'page'), [])) }}"
                       class="absolute inset-y-0 right-2 flex items-center text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        @foreach(\App\Models\Staff::contractLabels() as $type => $label)
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500 uppercase tracking-[0.18em] mb-3">{{ $label }}</p>
                <p class="text-3xl font-bold text-gray-900">{{ $contractCounts[$type] ?? 0 }}</p>
            </div>
        @endforeach
    </div>

    <div class="flex flex-wrap gap-3 mb-6">
        <a href="{{ route('staff.salaries.print') }}"
           target="_blank"
           class="inline-flex items-center px-4 py-2 rounded-lg bg-[#1A3A6B] text-white text-sm font-semibold hover:bg-[#14304f]">
            Imprimer la fiche de salaires
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Nom</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">E-mail</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Téléphone</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Contrat</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Salaire</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Poste</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($staff as $member)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4">
                                <a href="{{ route('staff.show', $member) }}" class="font-semibold text-gray-900 hover:text-blue-600">
                                    {{ $member->full_name }}
                                </a>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600">{{ $member->email ?? '—' }}</td>
                            <td class="px-4 py-4 text-sm text-gray-600">{{ $member->phone ?? '—' }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $member->contract_label }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $member->salary_display }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $member->primaryPosition?->position_label ?? 'Personnel' }}</td>
                            <td class="px-4 py-4 text-sm text-right">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('staff.pay-slip', $member) }}"
                                       target="_blank"
                                       class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100">
                                        Bulletin
                                    </a>
                                    @can('manage-staff')
                                        <a href="{{ route('staff.salary.edit', $member) }}"
                                           class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100">
                                            Modifier
                                        </a>
                                    @else
                                        —
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500">
                                Aucun membre trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $staff->links() }}
    </div>
</div>
@endsection
