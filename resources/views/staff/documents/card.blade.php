@extends('layouts.blank')

@section('title', $staff->full_name . ' — Carte professionnelle')

@section('content')
    @include('students.documents.partials.card-styles')

    <div class="flex justify-center py-8">
        @include('staff.documents.partials.card', ['staff' => $staff])
    </div>
@endsection
