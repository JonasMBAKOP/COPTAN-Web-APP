@extends('layouts.app')

@section('title', 'Modifier — ' . $staff->full_name)
@section('page-title', 'Modifier le dossier')
@section('page-subtitle'){{ $staff->full_name }}@endsection

@section('breadcrumb')
    <a href="{{ route('staff.index') }}" class="hover:text-gray-700">
        Personnel
    </a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round"
              stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <a href="{{ route('staff.show', $staff) }}" class="hover:text-gray-700">
        {{ $staff->full_name }}
    </a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round"
              stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span style="color:#1A3A6B;" class="font-medium">Modifier</span>
@endsection

@section('content')

<form method="POST" action="{{ route('staff.update', $staff) }}" enctype="multipart/form-data">
      {{-- x-data="staffEditForm({{ json_encode($staff->positions->map(fn($p) => ['name' => $p->position, 'primary' => $p->is_primary])) }})"> --}}
    @csrf @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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
                               value="{{ old('last_name', $staff->last_name) }}"
                               class="w-full px-3 py-2.5 border rounded-lg
                                      text-sm uppercase focus:outline-none
                                      @error('last_name') border-red-400
                                      @else border-gray-200 @enderror">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Prénom(s) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="first_name"
                               value="{{ old('first_name', $staff->first_name) }}"
                               class="w-full px-3 py-2.5 border rounded-lg
                                      text-sm focus:outline-none
                                      @error('first_name') border-red-400
                                      @else border-gray-200 @enderror">
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
                                           flex-1 text-sm
                                           {{ old('gender', $staff->gender) === $val
                                               ? 'border-blue-400 bg-blue-50'
                                               : 'border-gray-200 hover:bg-gray-50' }}">
                                <input type="radio" name="gender" value="{{ $val }}"
                                       {{ old('gender', $staff->gender) === $val
                                           ? 'checked' : '' }}
                                       style="accent-color:#1A3A6B;">
                                {{ $lbl }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date de naissance
                        </label>
                        <input type="date" name="date_of_birth"
                               value="{{ old('date_of_birth',
                                   $staff->date_of_birth?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2.5 border border-gray-200
                                      rounded-lg text-sm focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Téléphone
                        </label>
                        <input type="text" name="phone"
                               value="{{ old('phone', $staff->phone) }}"
                               class="w-full px-3 py-2.5 border border-gray-200
                                      rounded-lg text-sm focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Email
                        </label>
                        <input type="email" name="email"
                               value="{{ old('email', $staff->email) }}"
                               class="w-full px-3 py-2.5 border rounded-lg
                                      text-sm focus:outline-none
                                      @error('email') border-red-400
                                      @else border-gray-200 @enderror">
                    </div>
                </div>
            </div>

            {{-- Infos pro --}}
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
                               value="{{ old('diploma', $staff->diploma) }}"
                               class="w-full px-3 py-2.5 border border-gray-200
                                      rounded-lg text-sm focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date d'embauche
                        </label>
                        <input type="date" name="start_date"
                               value="{{ old('start_date',
                                   $staff->start_date?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2.5 border border-gray-200
                                      rounded-lg text-sm focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Type de contrat <span class="text-red-500">*</span>
                        </label>
                        <select name="contract_type"
                                class="w-full px-3 py-2.5 border border-gray-200
                                       rounded-lg text-sm focus:outline-none bg-white">
                            @foreach([
                                'permanent' => 'Permanent',
                                'temporary' => 'Temporaire / CDD',
                                'part_time' => 'Temps partiel',
                                'volunteer' => 'Bénévole',
                            ] as $val => $lbl)
                            <option value="{{ $val }}"
                                    {{ old('contract_type',
                                        $staff->contract_type) === $val
                                        ? 'selected' : '' }}>
                                {{ $lbl }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Postes --}}
            {{-- <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4 pb-2
                            border-b border-gray-100">
                    <h3 class="text-sm font-semibold uppercase tracking-wider
                               text-gray-400">
                        Poste(s)
                    </h3>
                    <button type="button" @click="addPosition()"
                            class="text-xs font-medium hover:underline"
                            style="color:#1A5C2A;">
                        + Ajouter
                    </button>
                </div>
                <div class="space-y-3">
                    <template x-for="(pos, i) in positions" :key="i">
                        <div class="flex items-center gap-3 p-3 border
                                    border-gray-200 rounded-xl bg-gray-50">
                            <div class="flex-1">
                                <select :name="`positions[${i}][name]`"
                                        x-model="pos.name"
                                        class="w-full px-3 py-2 border
                                               border-gray-200 rounded-lg
                                               text-sm focus:outline-none
                                               bg-white">
                                    @foreach([
                                        'enseignant'          => 'Enseignant(e)',
                                        'directeur'           => 'Directeur / Principal',
                                        'fondateur'           => 'Fondateur / Fondatrice',
                                        'censeur'             => 'Censeur / Préfet',
                                        'econome'             => 'Économe',
                                        'surveillant_general' => 'Surveillant(e) Gén.',
                                        'secretaire'          => 'Secrétaire',
                                    ] as $val => $lbl)
                                    <option value="{{ $val }}">{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label class="flex items-center gap-2 text-sm
                                           text-gray-600 whitespace-nowrap">
                                <input type="checkbox"
                                       :name="`positions[${i}][primary]`"
                                       value="1"
                                       :checked="pos.primary"
                                       @change="setPrimary(i)"
                                       style="accent-color:#1A3A6B;">
                                Principal
                            </label>
                            <button type="button" @click="removePosition(i)"
                                    x-show="positions.length > 1"
                                    class="p-1 text-gray-400 hover:text-red-500">
                                <svg class="w-4 h-4" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round" stroke-width="2"
                                          d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div> --}}

            {{-- Liaison compte --}}
            {{-- <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider
                           text-gray-400 mb-4 pb-2 border-b border-gray-100">
                    Liaison compte utilisateur
                </h3>
                <select name="user_id"
                        class="w-full px-3 py-2.5 border border-gray-200
                               rounded-lg text-sm focus:outline-none bg-white">
                    <option value="">Aucun compte associé</option>
                    @foreach($availableUsers as $user)
                    <option value="{{ $user->id }}"
                            {{ old('user_id', $staff->user_id) == $user->id
                                ? 'selected' : '' }}>
                        {{ $user->name }} — {{ $user->email }}
                    </option>
                    @endforeach
                </select>
            </div> --}}
            @include('staff._positions_and_user', ['staff' => $staff])

        </div>

        {{-- Colonne droite --}}
        <div class="space-y-4">

            {{-- Photo --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5"
                 x-data="{ preview: '{{ $staff->photo ? asset('storage/' . $staff->photo) : '' }}' }">
                <h3 class="text-sm font-semibold uppercase tracking-wider
                           text-gray-400 mb-4 pb-2 border-b border-gray-100">
                    Photo
                </h3>
                <div class="flex flex-col items-center gap-3">
                    <template x-if="preview">
                        <img :src="preview" alt="Photo"
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
                    <div class="w-full text-center">
                        <label class="cursor-pointer text-xs font-medium
                                       hover:underline"
                               style="color:#1A3A6B;">
                            Changer la photo
                            <input type="file" name="photo" class="hidden"
                                   accept="image/*"
                                   @change="preview = URL.createObjectURL($event.target.files[0])">
                        </label>
                        @if($staff->photo)
                        <form method="POST"
                              action="{{ route('staff.photo.delete', $staff) }}"
                              class="inline ml-3">
                            {{-- @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-xs text-red-500 hover:underline">
                                Supprimer
                            </button> --}}
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Statut --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Dossier actif</p>
                        <p class="text-xs text-gray-400">
                            Activer / désactiver le dossier
                        </p>
                    </div>
                    <label class="relative inline-flex cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                               {{ $staff->is_active ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer
                                    peer-checked:bg-green-500
                                    after:content-[''] after:absolute
                                    after:top-[2px] after:left-[2px]
                                    after:bg-white after:rounded-full
                                    after:h-5 after:w-5 after:transition-all
                                    peer-checked:after:translate-x-full"></div>
                    </label>
                </div>
            </div>

            {{-- Infos dossier --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-sm font-semibold uppercase tracking-wider
                           text-gray-400 mb-3 pb-2 border-b border-gray-100">
                    Informations du dossier
                </h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Créé le</dt>
                        <dd class="font-medium text-gray-700">
                            {{ $staff->created_at->format('d/m/Y') }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Cours assignés</dt>
                        <dd class="font-medium text-gray-700">
                            {{ $staff->teacher_assignments_count }}
                        </dd>
                    </div>
                </dl>
            </div>

            <button type="submit"
                    class="w-full py-3.5 rounded-xl text-white font-bold text-sm
                           flex items-center justify-center gap-2 transition-all
                           hover:shadow-md"
                    style="background-color:#1A3A6B;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0
                             002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3
                             3V4"/>
                </svg>
                Enregistrer les modifications
            </button>

            <a href="{{ route('staff.show', $staff) }}"
               class="block w-full py-2.5 rounded-xl text-center text-sm
                      font-medium text-gray-600 border border-gray-200
                      hover:bg-gray-50">
                Annuler
            </a>
        </div>
    </div>
</form>

<script>
// function staffEditForm(initialPositions) {
//     return {
//         positions: initialPositions.length > 0
//             ? initialPositions
//             : [{ name: 'enseignant', primary: true }],

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
</script>

@endsection