{{--
    Partial formulaire personnel
    Variables : $staff (nullable), $positionLabels, $contractLabels, $diplomas,
                $availableUsers, $roles (create only)
--}}

@php
    $isEdit = isset($staff);
    $selectedPositions = old('positions', $isEdit
        ? $staff->positions->pluck('position')->toArray()
        : ['enseignant']);
@endphp

{{-- ── IDENTITÉ ─────────────────────────────────────────────────────── --}}
<h2 class="text-sm font-semibold uppercase tracking-wider mb-4 pb-2
           border-b border-gray-100"
    style="color: #1A3A6B;">
    Identité
</h2>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Nom <span class="text-red-500">*</span>
        </label>
        <input type="text" name="last_name"
               value="{{ old('last_name', $staff->last_name ?? '') }}"
               placeholder="Ex: NTANKEU"
               class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none
                      focus:border-blue-400 @error('last_name') border-red-400 @else border-gray-200 @enderror">
        @error('last_name')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Prénom <span class="text-red-500">*</span>
        </label>
        <input type="text" name="first_name"
               value="{{ old('first_name', $staff->first_name ?? '') }}"
               placeholder="Ex: Jean-Paul"
               class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none
                      focus:border-blue-400 @error('first_name') border-red-400 @else border-gray-200 @enderror">
        @error('first_name')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Sexe <span class="text-red-500">*</span>
        </label>
        <select name="gender"
                class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none
                       focus:border-blue-400 bg-white
                       @error('gender') border-red-400 @else border-gray-200 @enderror">
            <option value="">Sélectionner...</option>
            <option value="M" {{ old('gender', $staff->gender ?? '') === 'M' ? 'selected' : '' }}>
                Masculin
            </option>
            <option value="F" {{ old('gender', $staff->gender ?? '') === 'F' ? 'selected' : '' }}>
                Féminin
            </option>
        </select>
        @error('gender')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Date de naissance
        </label>
        <input type="date" name="date_of_birth"
               value="{{ old('date_of_birth', isset($staff) && $staff->date_of_birth
                   ? $staff->date_of_birth->format('Y-m-d') : '') }}"
               class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm
                      focus:outline-none focus:border-blue-400">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Photo</label>
        @if($isEdit && $staff->photo)
        <div class="flex items-center gap-3 mb-2">
            <img src="{{ $staff->photo_url }}" alt=""
                 class="w-12 h-12 rounded-full object-cover ring-2 ring-gray-100">
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="remove_photo" value="1"
                       class="rounded" style="accent-color: #1A3A6B;">
                Supprimer la photo actuelle
            </label>
        </div>
        @endif
        <input type="file" name="photo" accept="image/jpeg,image/png"
               class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4
                      file:rounded-lg file:border-0 file:text-sm file:font-medium
                      file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        @error('photo')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

</div>

{{-- ── CONTACTS ─────────────────────────────────────────────────────── --}}
<h2 class="text-sm font-semibold uppercase tracking-wider mb-4 pb-2
           border-b border-gray-100"
    style="color: #1A3A6B;">
    Contacts
</h2>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
        <input type="text" name="phone"
               value="{{ old('phone', $staff->phone ?? '') }}"
               placeholder="Ex: +237 6XX XXX XXX"
               class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm
                      focus:outline-none focus:border-blue-400">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">E-mail professionnel</label>
        <input type="email" name="email"
               value="{{ old('email', $staff->email ?? '') }}"
               placeholder="exemple@coptan.cm"
               class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm
                      focus:outline-none focus:border-blue-400">
    </div>

</div>

{{-- ── CARRIÈRE ─────────────────────────────────────────────────────── --}}
<h2 class="text-sm font-semibold uppercase tracking-wider mb-4 pb-2
           border-b border-gray-100"
    style="color: #1A3A6B;">
    Carrière
</h2>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Diplôme le plus élevé
        </label>
        <select name="diploma"
                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm
                       focus:outline-none focus:border-blue-400 bg-white">
            <option value="">Non renseigné</option>
            @foreach($diplomas as $diploma)
            <option value="{{ $diploma }}"
                    {{ old('diploma', $staff->diploma ?? '') === $diploma ? 'selected' : '' }}>
                {{ $diploma }}
            </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Date d'entrée
        </label>
        <input type="date" name="start_date"
               value="{{ old('start_date', isset($staff) && $staff->start_date
                   ? $staff->start_date->format('Y-m-d') : '') }}"
               class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm
                      focus:outline-none focus:border-blue-400">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Type de contrat <span class="text-red-500">*</span>
        </label>
        <select name="contract_type"
                class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none
                       focus:border-blue-400 bg-white
                       @error('contract_type') border-red-400 @else border-gray-200 @enderror">
            @foreach($contractLabels as $value => $label)
            <option value="{{ $value }}"
                    {{ old('contract_type', $staff->contract_type ?? 'permanent') === $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
            @endforeach
        </select>
        @error('contract_type')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

</div>

{{-- ── POSTES ───────────────────────────────────────────────────────── --}}
<h2 class="text-sm font-semibold uppercase tracking-wider mb-4 pb-2
           border-b border-gray-100"
    style="color: #1A3A6B;">
    Postes occupés <span class="text-red-500">*</span>
</h2>

@error('positions')
    <p class="mb-3 text-xs text-red-500">{{ $message }}</p>
@enderror
@error('primary_position')
    <p class="mb-3 text-xs text-red-500">{{ $message }}</p>
@enderror

<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">

    @foreach($positionLabels as $value => $label)
    <label class="flex items-start gap-3 p-3 rounded-lg border cursor-pointer
                  transition-colors hover:bg-gray-50"
           :class="positions.includes('{{ $value }}')
               ? 'border-blue-400 bg-blue-50' : 'border-gray-200'">
        <input type="checkbox" name="positions[]" value="{{ $value }}"
               {{ in_array($value, $selectedPositions) ? 'checked' : '' }}
               @change="if ($event.target.checked) {
                   if (!positions.includes('{{ $value }}')) positions.push('{{ $value }}');
               } else {
                   positions = positions.filter(p => p !== '{{ $value }}');
                   if (primary === '{{ $value }}') primary = positions[0] || '';
               }"
               class="mt-0.5 rounded" style="accent-color: #1A3A6B;">
        <div>
            <p class="text-sm font-medium text-gray-800">{{ $label }}</p>
        </div>
    </label>
    @endforeach

    <div class="sm:col-span-2 mt-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Poste principal <span class="text-red-500">*</span>
        </label>
        <select x-model="primary"
                class="w-full sm:max-w-xs px-3 py-2.5 border border-gray-200
                       rounded-lg text-sm focus:outline-none focus:border-blue-400 bg-white">
            <template x-for="pos in positions" :key="pos">
                <option :value="pos" x-text="positionLabels[pos] || pos"></option>
            </template>
        </select>
        <p class="mt-1 text-xs text-gray-400">
            Sélectionnez au moins un poste ci-dessus.
        </p>
    </div>
</div>

@if($isEdit)
{{-- ── STATUT (édition) ─────────────────────────────────────────────── --}}
<div class="mb-6">
    <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200
                  cursor-pointer hover:bg-gray-50 w-fit">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1"
               {{ old('is_active', $staff->is_active) ? 'checked' : '' }}
               class="rounded" style="accent-color: #1A3A6B;">
        <span class="text-sm font-medium text-gray-700">Membre actif</span>
    </label>
</div>
@endif

{{-- ── COMPTE UTILISATEUR ─────────────────────────────────────────────── --}}
<h2 class="text-sm font-semibold uppercase tracking-wider mb-4 pb-2
           border-b border-gray-100"
    style="color: #1A3A6B;">
    Compte utilisateur
</h2>

<div class="mb-6">

    @unless($isEdit)
    <div class="flex flex-wrap gap-2 mb-4">
        @foreach([
            'none'   => 'Sans compte',
            'link'   => 'Lier un compte existant',
            'create' => 'Créer un nouveau compte',
        ] as $key => $label)
        <button type="button" @click="accountMode = '{{ $key }}'"
                :class="accountMode === '{{ $key }}'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
            {{ $label }}
        </button>
        @endforeach
    </div>
    @endunless

    {{-- Lier compte existant --}}
    <div x-show="accountMode === 'link' || {{ $isEdit ? 'true' : 'false' }}" x-transition>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Compte utilisateur lié
        </label>
        <select name="user_id"
                :disabled="accountMode !== 'link' && {{ $isEdit ? 'false' : 'true' }}"
                class="w-full sm:max-w-md px-3 py-2.5 border border-gray-200
                       rounded-lg text-sm focus:outline-none focus:border-blue-400 bg-white
                       disabled:bg-gray-50 disabled:text-gray-400">
            <option value="">Aucun compte lié</option>
            @foreach($availableUsers as $user)
            <option value="{{ $user->id }}"
                    {{ (string) old('user_id', $staff->user_id ?? '') === (string) $user->id ? 'selected' : '' }}>
                {{ $user->name }} — {{ $user->email }}
            </option>
            @endforeach
        </select>
        @error('user_id')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-xs text-gray-400">
            Seuls les comptes non encore liés à une fiche personnel sont proposés.
        </p>
    </div>

    {{-- Créer compte --}}
    @unless($isEdit)
    <div x-show="accountMode === 'create'" x-transition class="space-y-4 mt-2">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    E-mail de connexion <span class="text-red-500">*</span>
                </label>
                <input type="email" name="account_email" value="{{ old('account_email') }}"
                       placeholder="nom@coptan.cm"
                       class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none
                              focus:border-blue-400 @error('account_email') border-red-400 @else border-gray-200 @enderror">
                @error('account_email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Mot de passe <span class="text-red-500">*</span>
                </label>
                <input type="password" name="account_password"
                       class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none
                              focus:border-blue-400 @error('account_password') border-red-400 @else border-gray-200 @enderror">
                @error('account_password')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Confirmer le mot de passe
                </label>
                <input type="password" name="account_password_confirmation"
                       class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm
                              focus:outline-none focus:border-blue-400">
            </div>
        </div>

        <div>
            <p class="text-sm font-medium text-gray-700 mb-2">Rôle(s) du compte</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach($roles as $role)
                <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-200
                              cursor-pointer hover:bg-gray-50">
                    <input type="checkbox" name="account_roles[]" value="{{ $role->name }}"
                           {{ in_array($role->name, old('account_roles', ['enseignant'])) ? 'checked' : '' }}
                           class="rounded" style="accent-color: #1A3A6B;">
                    <span class="text-sm text-gray-700">
                        {{ ucfirst(str_replace('-', ' ', $role->name)) }}
                    </span>
                </label>
                @endforeach
            </div>
        </div>
    </div>
    @endunless
</div>
