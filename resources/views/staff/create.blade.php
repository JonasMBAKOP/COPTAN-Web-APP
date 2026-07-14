@extends('layouts.app')

@section('title', 'Nouveau membre')
@section('page-title', 'Nouveau Membre du Personnel')
@section('page-subtitle', 'Créer un dossier dans le système')

@section('breadcrumb')
    <a href="{{ route('staff.index') }}" class="hover:text-gray-700">
        Personnel
    </a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round"
              stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span style="color:#1A3A6B;" class="font-medium">Nouveau membre</span>
@endsection

@section('content')

{{-- <form method="POST" action="{{ route('staff.store') }}"
      enctype="multipart/form-data"
      x-data="staffForm()"> --}}
<form method="POST" action="{{ route('staff.store') }}" enctype="multipart/form-data"
      x-data="salaryContractForm('{{ old('contract_type', 'permanent') }}')">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Colonne principale ────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Identité --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider
                           text-gray-400 mb-4 pb-2 border-b border-gray-100">
                    Identité
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="last_name"
                               value="{{ old('last_name') }}"
                               placeholder="Ex: KAMGA"
                               class="w-full px-3 py-2.5 border rounded-lg text-sm uppercase focus:outline-none focus:ring-2 focus:ring-blue-200 {{ $errors->has('last_name') ? 'border-red-400' : 'border-gray-200' }}">
                        @error('last_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Prénom(s) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="first_name"
                               value="{{ old('first_name') }}"
                               placeholder="Ex: Jean-Paul"
                               class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 {{ $errors->has('first_name') ? 'border-red-400' : 'border-gray-200' }}">
                        @error('first_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Genre <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-3">
                            @foreach(['M' => 'Masculin', 'F' => 'Féminin']
                                as $val => $lbl)
                            <label class="flex items-center gap-2 px-4 py-2.5
                                           border rounded-lg cursor-pointer
                                           transition-colors flex-1 text-sm
                                           {{ old('gender') === $val
                                               ? 'border-blue-400 bg-blue-50'
                                               : 'border-gray-200 hover:bg-gray-50' }}">
                                <input type="radio" name="gender"
                                       value="{{ $val }}"
                                       {{ old('gender') === $val
                                           ? 'checked' : '' }}
                                       style="accent-color:#1A3A6B;">
                                {{ $lbl }}
                            </label>
                            @endforeach
                        </div>
                        @error('gender')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date de naissance
                        </label>
                        <input type="date" name="date_of_birth"
                               value="{{ old('date_of_birth') }}"
                               class="w-full px-3 py-2.5 border border-gray-200
                                      rounded-lg text-sm focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Téléphone
                        </label>
                        <input type="text" name="phone"
                               value="{{ old('phone') }}"
                               placeholder="+237 6XX XXX XXX"
                               class="w-full px-3 py-2.5 border border-gray-200
                                      rounded-lg text-sm focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Email professionnel
                        </label>
                        <input type="email" name="email"
                               value="{{ old('email') }}"
                               placeholder="prenom.nom@coptan.cm"
                               class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none {{ $errors->has('email') ? 'border-red-400' : 'border-gray-200' }}">
                        @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- Informations professionnelles --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider
                           text-gray-400 mb-4 pb-2 border-b border-gray-100">
                    Informations professionnelles
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Diplôme / Qualification
                        </label>
                        <input type="text" name="diploma"
                               value="{{ old('diploma') }}"
                               placeholder="Ex: Master en Mathématiques — Univ. Yaoundé I"
                               class="w-full px-3 py-2.5 border border-gray-200
                                      rounded-lg text-sm focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date d'embauche
                        </label>
                        <input type="date" name="start_date"
                               value="{{ old('start_date') }}"
                               class="w-full px-3 py-2.5 border border-gray-200
                                      rounded-lg text-sm focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Type de contrat <span class="text-red-500">*</span>
                        </label>
                        <select name="contract_type"
                                x-model="contractType"
                                class="w-full px-3 py-2.5 border border-gray-200
                                       rounded-lg text-sm focus:outline-none
                                       bg-white">
                            @foreach([
                                'permanent' => 'Permanent',
                                'vacataire' => 'Vacataire',
                                'stagiaire' => 'Stagiaire',
                            ] as $val => $lbl)
                            <option value="{{ $val }}"
                                    {{ old('contract_type') === $val
                                        ? 'selected' : '' }}>
                                {{ $lbl }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-6" x-show="contractType === 'permanent'" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Salaire mensuel
                        </label>
                        <input type="number" name="monthly_salary"
                               x-bind:disabled="contractType !== 'permanent'"
                               value="{{ old('monthly_salary') }}"
                               min="0" step="100"
                               class="w-full px-3 py-2.5 border border-gray-200
                                      rounded-lg text-sm focus:outline-none">
                    </div>
                    <div class="sm:col-span-6" x-show="contractType !== 'permanent'" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tarif horaire
                        </label>
                        <input type="number" name="hourly_rate"
                               x-bind:disabled="contractType === 'permanent'"
                               value="{{ old('hourly_rate') }}"
                               min="0" step="50"
                               class="w-full px-3 py-2.5 border border-gray-200
                                      rounded-lg text-sm focus:outline-none">
                    </div>
                </div>
            </div>



            {{-- Liaison compte utilisateur --}}
            {{-- <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider
                           text-gray-400 mb-4 pb-2 border-b border-gray-100">
                    Liaison compte utilisateur
                    <span class="text-gray-300 font-normal ml-1 normal-case
                                 tracking-normal">
                        (optionnel)
                    </span>
                </h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Compte de connexion associé
                    </label>
                    <select name="user_id"
                            class="w-full px-3 py-2.5 border border-gray-200
                                   rounded-lg text-sm focus:outline-none bg-white">
                        <option value="">Aucun compte associé</option>
                        @foreach($availableUsers as $user)
                        <option value="{{ $user->id }}"
                                {{ old('user_id') == $user->id
                                    ? 'selected' : '' }}>
                            {{ $user->name }} — {{ $user->email }}
                            ({{ $user->roles->pluck('name')->join(', ') }})
                        </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-400">
                        Seuls les comptes sans dossier RH sont disponibles.
                    </p>
                </div>
            </div> --}}
            @include('staff._positions_and_user')

        </div>

        {{-- ── Colonne droite ────────────────────────────────────────── --}}
        <div class="space-y-4">

            {{-- Photo --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5"
                 x-data="photoUpload()">
                <h3 class="text-sm font-semibold uppercase tracking-wider
                           text-gray-400 mb-4 pb-2 border-b border-gray-100">
                    Photo
                </h3>

                <div class="flex flex-col items-center gap-4">
                    {{-- Aperçu --}}
                    <template x-if="preview">
                        <img :src="preview" alt="Aperçu"
                             class="w-28 h-28 rounded-full object-cover
                                    ring-4 ring-gray-100">
                    </template>
                    <template x-if="!preview">
                        <div class="w-28 h-28 rounded-full flex items-center
                                    justify-center"
                             style="background-color:#EBF3FB;">
                            <svg class="w-12 h-12" style="color:#1A3A6B;"
                                 fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round" stroke-width="1.5"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12
                                         14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    </template>

                    {{-- Upload --}}
                    <div class="w-full border-2 border-dashed border-gray-200
                                rounded-xl p-4 text-center cursor-pointer
                                hover:border-blue-300 hover:bg-blue-50/30
                                transition-colors"
                         @click="$refs.photoInput.click()">
                        <input type="file" name="photo" x-ref="photoInput"
                               class="hidden" accept="image/*"
                               @change="handleFile($event)">
                        <p class="text-xs text-gray-500">
                            Cliquer pour choisir
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            JPG, PNG — Max 2 Mo
                        </p>
                    </div>
                </div>
            </div>

            {{-- Statut --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-700">
                            Dossier actif
                        </p>
                        <p class="text-xs text-gray-400">
                            Un dossier inactif n'apparaît plus dans les listes
                        </p>
                    </div>
                    <label class="relative inline-flex cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                               checked class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer
                                    peer-checked:bg-green-500
                                    after:content-[''] after:absolute
                                    after:top-[2px] after:left-[2px]
                                    after:bg-white after:rounded-full
                                    after:h-5 after:w-5 after:transition-all
                                    peer-checked:after:translate-x-full">
                        </div>
                    </label>
                </div>
            </div>

            {{-- Bouton --}}
            <button type="submit"
                    class="w-full py-3.5 rounded-xl text-white font-bold text-sm
                           flex items-center justify-center gap-2 transition-all
                           hover:shadow-md"
                    style="background-color:#1A5C2A;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Créer le dossier
            </button>

            <a href="{{ route('staff.index') }}"
               class="block w-full py-2.5 rounded-xl text-center text-sm
                      font-medium text-gray-600 border border-gray-200
                      hover:bg-gray-50">
                Annuler
            </a>
        </div>

    </div>
</form>

<script>
// function staffForm() {
//     return {
//         positions: [{ name: '{{ old('positions.0.name', 'enseignant') }}',
//                       primary: true }],

//         addPosition() {
//             this.positions.push({ name: '', primary: false });
//         },
//         removePosition(i) {
//             const wasPrimary = this.positions[i].primary;
//             this.positions.splice(i, 1);
//             if (wasPrimary && this.positions.length > 0) {
//                 this.positions[0].primary = true;
//             }
//         },
//         setPrimary(i) {
//             if (this.positions[i].primary) {
//                 this.positions.forEach((p, j) => {
//                     if (j !== i) p.primary = false;
//                 });
//             }
//         }
//     }
// }

function photoUpload() {
    return {
        preview: null,
        handleFile(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (ev) => this.preview = ev.target.result;
            reader.readAsDataURL(file);
        }
    }
}

function salaryContractForm(initialContractType) {
    return {
        contractType: initialContractType || 'permanent',
    }
}
</script>

@endsection