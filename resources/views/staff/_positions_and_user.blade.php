@php
    $positionsList = [
        'enseignant'          => 'Enseignant(e)',
        'directeur'           => 'Directeur',
        'prefet_des_etudes'   => 'Préfet des études',
        'econome'             => 'Économe',
        'surveillant_general' => 'Surveillant général',
    ];

    $currentPositions = old('positions',
        isset($staff)
            ? $staff->positions->pluck('position')->toArray()
            : ['enseignant']
    );

    foreach ($currentPositions as $position) {
        if (! array_key_exists($position, $positionsList)) {
            $positionsList[$position] = \App\Models\Staff::positionLabels()[$position]
                ?? ucfirst(str_replace(['-', '_'], ' ', $position));
        }
    }
    $currentPrimary = old('primary_position',
        isset($staff)
            ? ($staff->positions->where('is_primary', true)->first()?->position)
            : 'enseignant'
    );

    $roleLabels = [
        'super-admin'         => 'Super-admin',
        'directeur'           => 'Directeur',
        'censeur'             => 'Préfet des études',
        'econome'             => 'Économe',
        'surveillant-general' => 'Surveillant général',
        'enseignant'          => 'Enseignant',
    ];

    $roleOrder = [
        'super-admin',
        'directeur',
        'censeur',
        'econome',
        'surveillant-general',
        'enseignant',
    ];
@endphp

{{-- ── POSTES ─────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-400
               mb-4 pb-2 border-b border-gray-100">
        Poste(s) occupé(s) <span class="text-red-500">*</span>
    </h3>
    @error('positions')
    <p class="mb-3 text-xs text-red-500 bg-red-50 px-3 py-2 rounded-lg">
        {{ $message }}
    </p>
    @enderror

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        @foreach($positionsList as $val => $lbl)
        @php $isChecked = in_array($val, $currentPositions); @endphp
        <div class="flex items-center justify-between p-3 border rounded-xl
                    cursor-pointer transition-colors
                    {{ $isChecked ? 'border-blue-200 bg-blue-50' : 'border-gray-200 hover:bg-gray-50' }}">
            <label class="flex items-center gap-2.5 cursor-pointer flex-1">
                <input type="checkbox"
                       name="positions[]"
                       value="{{ $val }}"
                       {{ $isChecked ? 'checked' : '' }}
                       class="w-4 h-4 rounded"
                       style="accent-color:#1A3A6B;"
                       onchange="toggleCard(this)">
                <span class="text-sm font-medium text-gray-700">
                    {{ $lbl }}
                </span>
            </label>
            {{-- Radio "Principal" --}}
            <label class="flex items-center gap-1.5 text-xs text-gray-500
                           cursor-pointer {{ $isChecked ? '' : 'opacity-30' }}"
                   title="Définir comme poste principal">
                <input type="radio"
                       name="primary_position"
                       value="{{ $val }}"
                       {{ $currentPrimary === $val ? 'checked' : '' }}
                       class="w-3.5 h-3.5"
                       style="accent-color:#E87722;">
                Principal
            </label>
        </div>
        @endforeach
    </div>
    <p class="mt-2 text-xs text-gray-400">
        Cochez au moins un poste et marquez le poste principal avec le bouton
        <span style="color:#E87722;">Principal</span>.
    </p>
</div>

{{-- ── LIAISON COMPTE UTILISATEUR ────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6"
     x-data="{
         mode: '{{ old('user_option', isset($staff) && $staff->user_id ? 'existing' : 'none') }}'
     }">
    <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-400
               mb-4 pb-2 border-b border-gray-100">
        Compte de connexion
        <span class="text-gray-300 font-normal normal-case tracking-normal ml-1">
            (optionnel)
        </span>
    </h3>

    {{-- Sélecteur de mode --}}
    <div class="grid grid-cols-3 gap-2 mb-4 p-1 bg-gray-100 rounded-xl">
        @foreach([
            'none'     => 'Aucun compte',
            'existing' => 'Compte existant',
            'create'   => 'Créer un compte',
        ] as $val => $lbl)
        <button type="button"
                @click="mode = '{{ $val }}'"
                :class="mode === '{{ $val }}'
                    ? 'bg-white shadow-sm font-semibold'
                    : 'text-gray-500 hover:text-gray-700'"
                class="px-3 py-2 rounded-lg text-xs transition-all text-center">
            {{ $lbl }}
        </button>
        @endforeach
    </div>
    <input type="hidden" name="user_option" x-model="mode">

    {{-- Mode : aucun --}}
    <div x-show="mode === 'none'" class="text-sm text-gray-400 italic">
        Ce membre n'aura pas de compte de connexion.
    </div>

    {{-- Mode : compte existant --}}
    <div x-show="mode === 'existing'" x-transition>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Sélectionner un compte
        </label>
        <select name="user_id"
                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg
                       text-sm focus:outline-none bg-white">
            <option value="">— Choisir un compte —</option>
            @foreach($availableUsers as $user)
            <option value="{{ $user->id }}"
                    {{ old('user_id', isset($staff) ? $staff->user_id : null)
                        == $user->id ? 'selected' : '' }}>
                {{ $user->name }} ({{ $user->email }})
                @if($user->roles->isNotEmpty())
                — {{ $user->roles->pluck('name')->join(', ') }}
                @endif
            </option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-gray-400">
            Seuls les comptes sans dossier RH sont listés.
        </p>
    </div>

    {{-- Mode : créer un compte --}}
    <div x-show="mode === 'create'" x-transition>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nom complet <span class="text-red-500">*</span>
                </label>
                <input type="text" name="new_user_name"
                       value="{{ old('new_user_name') }}"
                       placeholder="Ex: KAMGA Jean-Paul"
                       class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none {{ $errors->has('new_user_name') ? 'border-red-400' : 'border-gray-200' }}">
                @error('new_user_name')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" name="new_user_email"
                       value="{{ old('new_user_email') }}"
                       placeholder="email@coptan.cm"
                       class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none {{ $errors->has('new_user_email') ? 'border-red-400' : 'border-gray-200' }}">
                @error('new_user_email')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Mot de passe <span class="text-red-500">*</span>
                </label>
                <input type="password" name="new_user_password"
                       placeholder="••••••••"
                       class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none {{ $errors->has('new_user_password') ? 'border-red-400' : 'border-gray-200' }}">
                @error('new_user_password')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Rôle
                </label>
                <select name="new_user_role"
                        class="w-full px-3 py-2.5 border border-gray-200
                               rounded-lg text-sm focus:outline-none bg-white">
                    <option value="">Sans rôle spécifique</option>
                    @foreach($roles ?? \Spatie\Permission\Models\Role::orderBy('name')->get() as $role)
                    <option value="{{ $role->name }}"
                            {{ old('new_user_role') === $role->name
                                ? 'selected' : '' }}>
                        {{ $roleLabels[$role->name] ?? ucfirst(str_replace('-', ' ', $role->name)) }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-3 p-3 rounded-xl text-xs"
             style="background-color:#EBF3FB; color:#1A3A6B;">
            <div class="flex gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18
                             0 9 9 0 0118 0z"/>
                </svg>
                Un compte de connexion sera créé et lié automatiquement
                à ce dossier.
            </div>
        </div>
    </div>
</div>

<script>
function toggleCard(checkbox) {
    const card = checkbox.closest('[class*="border"]');
    if (checkbox.checked) {
        card.classList.add('border-blue-200', 'bg-blue-50');
        card.classList.remove('border-gray-200');
        card.querySelector('[type="radio"]').disabled = false;
        card.querySelector('[class*="opacity"]')
            ?.classList.remove('opacity-30');
    } else {
        card.classList.remove('border-blue-200', 'bg-blue-50');
        card.classList.add('border-gray-200');
        const radio = card.querySelector('[type="radio"]');
        radio.checked = false;
        radio.disabled = false;
        card.querySelector('[class*="opacity"]')
            ?.classList.add('opacity-30');
    }
}
</script>