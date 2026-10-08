{{--
    pages/besoin.blade.php — Page besoin autonome.
    ──────────────────────────────────────────────────────────────────────────
    Accessible via /{locale}/besoin.
--}}
@php
    $locale ??= 'fr';
    $t = fn($fr, $en) => $locale === 'en' ? $en : $fr;
@endphp

@extends('layouts.app')

@section('meta_title')
    {{ $t('Soumettre un besoin — OAAT', 'Submit a need — OAAT') }}
@endsection

@section('meta_description')
    {{ $t("Soumettez votre besoin à l'OAAT pour une prise en charge rapide.",
          "Submit your need to OAAT for quick follow-up.") }}
@endsection

@section('content')
    @include('partials._besoin', ['locale' => $locale])
@stop

@push('scripts')
    <script src="{{ asset('js/oaat.js') }}"></script>
@endpush