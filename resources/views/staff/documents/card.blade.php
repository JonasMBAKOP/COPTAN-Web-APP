@extends('layouts.blank')

@section('title', $staff->full_name . ' — Carte professionnelle')

@section('content')
    @include('students.documents.partials.card-styles')

    <style>
        body {
            background: #f3f4f6;
        }
        .staff-card-preview {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            padding: 16px 0 24px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }
        .staff-card-preview .id-card {
            transform: scale(1);
        }
    </style>

    @include('students.documents.partials.print-toolbar')

    <div class="staff-card-preview">
        @include('staff.documents.partials.card', ['staff' => $staff])
    </div>
@endsection
