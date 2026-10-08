{{--
    pages/contact.blade.php — Page contact autonome.
    ──────────────────────────────────────────────────────────────────────────
    Accessible via /{locale}/contact.
    Utilise la même partial _contact que la page d'accueil.
--}}
@php
    $locale ??= 'fr';
    $t = fn($fr, $en) => $locale === 'en' ? $en : $fr;
@endphp

@extends('layouts.app')

@section('meta_title')
    {{ $t('Contact — OAAT', 'Contact — OAAT') }}
@endsection

@section('meta_description')
    {{ $t("Contactez l'OAAT pour vos propositions de partenariat, financement ou demande d'information.",
          "Contact OAAT for partnership proposals, funding or information requests.") }}
@endsection

@section('content')

    {{-- ═══════ CONTACT ═════════════════════════════════════════ --}}
    @include('partials._contact', [
        'locale'            => $locale,
    ])

@stop

@push('scripts')
    <script src="{{ asset('js/oaat.js') }}"></script>
@endpush