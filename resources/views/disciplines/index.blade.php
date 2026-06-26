<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Discipline</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $activeYear->name }} — Gestion des incidents disciplinaires</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">

        {{-- ── Statistiques ─────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 px-4 sm:px-6 lg:px-8">
            @php
                $statCards = [
                    ['label' => 'Total dossiers',   'value' => $stats['total'],      'color' => 'blue',   'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['label' => 'En cours',          'value' => $stats['ouverts'],    'color' => 'yellow', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['label' => 'Renvois temp.',     'value' => $stats['renvois'],    'color' => 'orange', 'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'],
                    ['label' => 'Exclusions déf.',   'value' => $stats['exclusions'], 'color' => 'red',    'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ];  
            @endphp

            @foreach($statCards as $card)
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center gap-4">
                    <div class="w-11 h-11 rounded-lg bg-{{ $card['color'] }}-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-{{ $card['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ $card['value'] }}</p>
                        <p class="text-xs text-gray-500">{{ $card['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── Filtres ───────────────────────────────────────────────────────── --}}
        <div class="px-4 sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('discipline.index') }}"
                  class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                    {{-- Recherche élève --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Nom ou matricule..."
                               class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    {{-- Classe --}}
                    <select name="class_id"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Statut --}}
                    <select name="status"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Tous les statuts</option>
                        <option value="ouvert"  {{ request('status') === 'ouvert'  ? 'selected' : '' }}>Ouvert</option>
                        <option value="resolu"  {{ request('status') === 'resolu'  ? 'selected' : '' }}>Résolu</option>
                        <option value="classe"  {{ request('status') === 'classe'  ? 'selected' : '' }}>Classé</option>
                    </select>

                    {{-- Sanction --}}
                    <select name="sanction_type"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Toutes les sanctions</option>
                        @foreach(\App\Models\DisciplineRecord::$sanctionTypes as $key => $label)
                            <option value="{{ $key }}" {{ request('sanction_type') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-3 flex gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Filtrer
                    </button>
                    @if(request()->hasAny(['search','class_id','status','sanction_type']))
                        <a href="{{ route('discipline.index') }}"
                           class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                            Réinitialiser
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- ── Table des dossiers ────────────────────────────────────────────── --}}
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

                @if($records->isEmpty())
                    <div class="py-16 text-center">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-gray-500 font-medium">Aucun dossier disciplinaire</p>
                        <p class="text-sm text-gray-400 mt-1">Les incidents enregistrés apparaîtront ici</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Élève</th>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Classe</th>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Date</th>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Incident</th>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Sanction</th>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Statut</th>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($records as $record)
                                    @php
                                        $sanctionColors = [
                                            'aucune'               => 'gray',
                                            'avertissement'        => 'yellow',
                                            'blame'                => 'orange',
                                            'retenue'              => 'blue',
                                            'renvoi_temporaire'    => 'red',
                                            'exclusion_definitive' => 'red',
                                        ];
                                        $statusColors = [
                                            'ouvert'  => 'yellow',
                                            'resolu'  => 'green',
                                            'classe'  => 'gray',
                                        ];
                                        $sc = $sanctionColors[$record->sanction_type] ?? 'gray';
                                        $stc = $statusColors[$record->status] ?? 'gray';
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3">
                                            <a href="{{ route('discipline.show', $record) }}"
                                               class="font-semibold text-gray-800 hover:text-blue-600 transition-colors">
                                                {{ $record->student->last_name }} {{ $record->student->first_name }}
                                            </a>
                                            <p class="text-xs text-gray-400">{{ $record->student->matricule }}</p>
                                        </td>
                                        <td class="px-4 py-3 text-gray-600">{{ $record->classe->name ?? '—' }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $record->incident_date->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ $record->incident_type_label }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                bg-{{ $sc }}-100 text-{{ $sc }}-700 border border-{{ $sc }}-200">
                                                {{ $record->sanction_type_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                bg-{{ $stc }}-100 text-{{ $stc }}-700 border border-{{ $stc }}-200">
                                                {{ $record->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('discipline.show', $record) }}"
                                                   class="text-blue-600 hover:text-blue-800 transition-colors" title="Voir">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                                @if($record->convocation_parent)
                                                    <a href="{{ route('discipline.convocation', $record) }}"
                                                       target="_blank"
                                                       class="text-purple-600 hover:text-purple-800 transition-colors" title="Convocation PDF">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                                <a href="{{ route('discipline.edit', $record) }}"
                                                   class="text-gray-400 hover:text-gray-600 transition-colors" title="Modifier">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($records->hasPages())
                        <div class="px-4 py-3 border-t border-gray-100">
                            {{ $records->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- ── FAB : Nouveau dossier ────────────────────────────────────────────── --}}
    <a href="{{ route('discipline.create') }}"
       class="fixed bottom-6 right-6 w-14 h-14 bg-red-600 hover:bg-red-700 text-white rounded-full shadow-lg
              flex items-center justify-center transition-all hover:scale-110 z-50"
       title="Nouveau dossier disciplinaire">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
    </a>
</x-app-layout>