@extends('layouts.app')

@section('title', 'Cartes du personnel')

@section('content')

<form method="POST" action="{{ route('staff.documents.cards.print') }}" target="_blank" id="bulk-card-form">
    @csrf

    <div class="rounded-3xl border border-slate-200 bg-gradient-to-br from-[#0F2746] via-[#1A3A6B] to-[#274d84] p-5 text-white shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.25em] text-white/90">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/>
                    </svg>
                    Cartes professionnelles
                </div>
                <h2 class="mt-3 text-xl font-semibold">Préparez une impression soignée pour l’équipe</h2>
                <p class="mt-1 text-sm text-slate-200">Sélectionnez les membres du personnel à imprimer et générez un lot prêt à l’emploi.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" id="toggle-select-all"
                        class="inline-flex items-center justify-center rounded-xl border border-white/25 bg-white/10 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/20">
                    Tout sélectionner
                </button>
                <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-[#1A3A6B] transition hover:bg-slate-100">
                    Imprimer la sélection
                </button>
            </div>
        </div>
    </div>

    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse($staff as $s)
            <div class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="ids[]" value="{{ $s->id }}" class="card-checkbox mt-1 h-4 w-4 rounded border-slate-300 text-[#1A3A6B] focus:ring-[#1A3A6B]">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="text-sm font-semibold text-slate-900">{{ $s->full_name }}</div>
                                <div class="mt-1 inline-flex rounded-full bg-[#F7F5FF] px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-[#5B4B8A]">
                                    {{ $s->primaryPosition ? (App\Models\Staff::positionLabels()[$s->primaryPosition->position] ?? $s->primaryPosition->position) : 'Personnel' }}
                                </div>
                            </div>
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-slate-500">Carte</span>
                        </div>
                        <div class="mt-3 space-y-2 text-xs text-slate-500">
                            <div class="flex items-center gap-2">
                                <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-7 0V4m0 2a2 2 0 100 4 2 2 0 000-4zm7 0V4m0 2a2 2 0 100 4 2 2 0 000-4z"/>
                                </svg>
                                <span>Matricule : {{ $s->employee_number ?? 'À renseigner' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h18M7 9v10m10-10v10"/>
                                </svg>
                                <span>{{ $s->phone ?? 'Téléphone à renseigner' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l9 6 9-6"/>
                                </svg>
                                <span>{{ $s->email ?? 'Email à renseigner' }}</span>
                            </div>
                        </div>
                    </div>
                </label>
                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3 text-xs text-slate-500">
                    <span class="font-semibold uppercase tracking-[0.2em] text-slate-400">Aperçu</span>
                    <a href="{{ route('staff.documents.single', $s) }}"
                       class="font-semibold text-[#1A3A6B] transition hover:text-[#17305a]">
                        Voir la carte
                    </a>
                </div>
            </div>
        @empty
            <div class="sm:col-span-2 xl:col-span-3 rounded-2xl border border-dashed border-slate-200 bg-white p-10 text-center text-sm text-slate-500">
                Aucun membre du personnel disponible pour la génération des cartes.
            </div>
        @endforelse
    </div>
</form>

@push('scripts')
<script>
    const form = document.getElementById('bulk-card-form');
    const toggleButton = document.getElementById('toggle-select-all');
    const checkboxes = Array.from(document.querySelectorAll('.card-checkbox'));
    let allSelected = false;

    toggleButton?.addEventListener('click', function (event) {
        event.preventDefault();
        allSelected = !allSelected;
        checkboxes.forEach(cb => cb.checked = allSelected);
        toggleButton.textContent = allSelected ? 'Tout désélectionner' : 'Tout sélectionner';
    });

    form?.addEventListener('submit', function (event) {
        const hasSelection = checkboxes.some(cb => cb.checked);
        if (!hasSelection) {
            event.preventDefault();
            alert('Sélectionnez au moins un membre du personnel pour imprimer.');
        }
    });
</script>
@endpush

@endsection
