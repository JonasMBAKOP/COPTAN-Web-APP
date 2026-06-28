@extends('layouts.app')

@section('title', 'Mon Profil')
@section('page-title', 'Mon Profil')
@section('page-subtitle', 'Gérez vos informations personnelles et la sécurité de votre compte')

@section('content')

{{-- @if(session('success'))
<div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-5">
    <p class="text-sm font-bold text-green-700">{{ session('success') }}</p>
</div>
@endif --}}

{{-- ── EN-TÊTE ───────────────────────────────────────────────────────────── --}}
<div class="rounded-2xl overflow-hidden mb-6"
     style="background:linear-gradient(135deg,#1A3A6B 0%,#0D2040 100%);">
    <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
        <div class="flex items-center gap-5">
            @if($user->photo)
            <img src="{{ asset('storage/' . $user->photo) }}"
                 class="w-20 h-20 rounded-full object-cover ring-4 ring-white/20">
            @else
            <div class="w-20 h-20 rounded-full flex items-center justify-center
                        text-white font-black text-2xl ring-4 ring-white/20"
                 style="background-color:rgba(255,255,255,.15);">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            @endif
            <div>
                <div class="flex items-center gap-2 flex-wrap mb-1">
                    <h2 class="text-xl font-black text-white">
                        {{ strtoupper($user->name) }}
                    </h2>
                    @foreach($user->roles as $role)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold
                                 bg-green-400/20 text-green-300 uppercase">
                        {{ ucfirst(str_replace('-', ' ', $role->name)) }}
                    </span>
                    @endforeach
                </div>
                <div class="flex items-center gap-4 text-sm text-white/70 flex-wrap">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0
                                     002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $user->email }}
                    </span>
                    @if($user->phone)
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1
                                     1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516
                                     5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0
                                     01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        {{ $user->phone }}
                    </span>
                    @endif
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955
                                     11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824
                                     10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133
                                     -2.052-.382-3.016z"/>
                        </svg>
                        Membre depuis {{ $user->created_at->format('M Y') }}
                    </span>
                </div>
            </div>
        </div>

        <a href="{{ route('profile.edit') }}"
           class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold
                  text-white border-2 border-white/30 hover:bg-white/10
                  transition-all whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536
                         3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
            Modifier mon profil
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- ── Informations personnelles ────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="flex items-center gap-2 text-sm font-black mb-5 pb-3
                   border-b border-gray-100" style="color:#1A3A6B;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Informations personnelles
        </h3>

        <dl class="space-y-4">
            <div>
                <dt class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-1">
                    Nom complet
                </dt>
                <dd class="text-sm font-semibold text-gray-800 pb-2 border-b border-gray-100">
                    {{ $user->name }}
                </dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-1">
                    Email institutionnel
                </dt>
                <dd class="text-sm font-semibold text-gray-800 pb-2 border-b border-gray-100">
                    {{ $user->email }}
                </dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-1">
                    Téléphone
                </dt>
                <dd class="text-sm font-semibold text-gray-800 pb-2 border-b border-gray-100">
                    {{ $user->phone ?? '—' }}
                </dd>
            </div>
            @if($user->staff)
            <div>
                <dt class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-1">
                    Poste
                </dt>
                <dd class="text-sm font-semibold text-gray-800">
                    {{ $user->staff->primaryPosition?->position_label ?? '—' }}
                </dd>
            </div>
            @endif
        </dl>
    </div>

    {{-- ── Sécurité ──────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="flex items-center gap-2 text-sm font-black mb-5 pb-3
                   border-b border-gray-100" style="color:#1A3A6B;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955
                         11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824
                         10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133
                         -2.052-.382-3.016z"/>
            </svg>
            Sécurité du compte
        </h3>

        <div class="space-y-4">
            <a href="{{ route('profile.edit') }}#password"
               class="flex items-center justify-between p-3 rounded-xl
                      border border-gray-200 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center"
                         style="background-color:#FEF3EA;">
                        <svg class="w-4 h-4" style="color:#E87722;" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2
                                     2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Mot de passe</p>
                        <p class="text-xs text-gray-400">Modifié pour la dernière fois récemment</p>
                    </div>
                </div>
                <span class="text-xs font-bold" style="color:#E87722;">Modifier →</span>
            </a>

            <div class="p-3 rounded-xl border border-gray-200">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-semibold text-gray-800">Session actuelle</p>
                    <span class="flex items-center gap-1 text-xs font-bold text-green-600">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                        Connecté(e)
                    </span>
                </div>
                <p class="text-xs text-gray-400">
                    Dernière connexion : {{ $user->updated_at->diffForHumans() }}
                </p>
            </div>

            <form method="POST" action="{{ route('profile.logout-others') }}"
                  onsubmit="return confirm('Déconnecter toutes les autres sessions ?')"
                  x-data="{ showPwd: false }">
                @csrf
                <button type="button" @click="showPwd = !showPwd"
                        class="text-xs font-bold hover:underline"
                        style="color:#1A3A6B;">
                    Déconnecter mes autres sessions
                </button>
                <div x-show="showPwd" x-transition class="mt-2 flex gap-2">
                    <input type="password" name="password" placeholder="Votre mot de passe"
                           required
                           class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm
                                  focus:outline-none">
                    <button type="submit"
                            class="px-4 py-2 rounded-lg text-white text-sm font-bold"
                            style="background-color:#EF4444;">
                        Confirmer
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection