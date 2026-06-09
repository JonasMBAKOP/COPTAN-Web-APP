@extends('layouts.app')

@section('title', 'Économe')
@section('page-title', 'Tableau de bord')
@section('page-subtitle', 'Espace Économe')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 lg:gap-6">

    {{-- KPI 1 --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Élèves</p>
                <p class="text-2xl font-bold mt-1" style="color: #1A3A6B;">0</p>
            </div>
            <div class="w-12 h-12 rounded-full flex items-center justify-center"
                 style="background-color: #EBF3FB;">
                <svg class="w-6 h-6" style="color: #1A3A6B;" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6
                             6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- KPI 2 --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Classes actives</p>
                <p class="text-2xl font-bold mt-1 text-green-600">0</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9
                             0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1
                             1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- KPI 3 --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Frais collectés</p>
                <p class="text-2xl font-bold mt-1" style="color: #C8A415;">0 FCFA</p>
            </div>
            <div class="w-12 h-12 rounded-full flex items-center justify-center"
                 style="background-color: #FBF5E6;">
                <svg class="w-6 h-6" style="color: #C8A415;" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2
                             2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2
                             2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- KPI 4 --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Taux de réussite</p>
                <p class="text-2xl font-bold mt-1 text-purple-600">—</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0
                             002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2
                             2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2
                             2 0 01-2-2z"/>
                </svg>
            </div>
        </div>
    </div>

</div>

{{-- Message de bienvenue --}}
<div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <h2 class="text-lg font-semibold mb-1" style="color: #1A3A6B;">
        Bienvenue, {{ auth()->user()->name }} 👋
    </h2>
    <p class="text-gray-500 text-sm">
        Espace de gestion — Économe
    </p>
</div>

@endsection