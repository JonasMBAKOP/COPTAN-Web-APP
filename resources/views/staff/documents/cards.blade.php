@extends('layouts.app')

@section('title', 'Cartes du personnel')

@section('content')

<form method="POST" action="{{ route('staff.documents.cards.print') }}" target="_blank" id="bulk-card-form">
    @csrf

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
        <div>
            <h2 class="text-lg font-semibold">Cartes professionnelles — Personnel</h2>
            <p class="text-sm text-gray-500">Sélectionnez un ou plusieurs membres du personnel pour imprimer leurs cartes.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" id="toggle-select-all"
                    class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                Tout sélectionner
            </button>
            <button type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-[#1A3A6B] px-4 py-2 text-sm font-semibold text-white hover:bg-[#17305a] transition">
                Imprimer la sélection
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($staff as $s)
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-lg">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="ids[]" value="{{ $s->id }}" class="card-checkbox mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-gray-900">{{ $s->full_name }}</div>
                        <div class="text-xs text-gray-500">{{ $s->position?->name }}</div>
                        <div class="mt-3 text-xs text-gray-500">Matricule : {{ $s->employee_number ?? '—' }}</div>
                    </div>
                </label>
                <div class="mt-4 text-xs text-gray-500">
                    Aperçu :
                    <a href="{{ route('staff.documents.single', $s) }}"
                       class="font-semibold text-[#1A3A6B] hover:text-[#17305a]">
                        Voir la carte
                    </a>
                </div>
            </div>
        @endforeach
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
