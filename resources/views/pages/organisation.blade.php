{{--
    pages/organisation.blade.php — Page Organisation.
    ────────────────────────────────────────────────────────────────
    Hérite de layouts.app (topbar, header, footer, toast).
    Variables disponibles (depuis OrganisationController) :
        $pageOrganisation, $settings, $founder, $yearsOfActivity,
        $totalProjects, $domainCount, $timeline, $recognitions,
        $zones, $locale
--}}
@php
    $locale ??= 'fr';
    $t = fn($fr, $en) => $locale === 'en' ? $en : $fr;
@endphp
@extends('layouts.app')

{{-- ─── SEO : titre & description dynamiques ─────────────────── --}}
@section('meta_title')
    {{ $pageOrganisation?->seoTitle($locale) ?: "OAAT — Organisation Africaine pour l'Aménagement des Territoires" }}
@endsection

@section('meta_description')
    {{ $pageOrganisation?->seoDescription($locale) ?: "Présentation de l'OAAT : histoire, mission, vision, fondateur, reconnaissance officielle et zones d'intervention." }}
@endsection

@section('content')

    {{-- ═══════ Hero interne réutilisable ═══════════════════════════ --}}
    @include('partials._hero_simple', [
        'page'   => $pageOrganisation,
        'stats'  => [
            ['valeur' => $yearsOfActivity ?? 0, 'fr' => "ans d'action depuis 1995", 'en' => 'years of action since 1995'],
            ['valeur' => $totalProjects ?? 0,   'fr' => 'projets réalisés',        'en' => 'projects completed'],
            ['valeur' => $domainCount ?? 0,     'fr' => "domaines d'intervention", 'en' => 'areas of intervention'],
        ],
        'locale' => $locale,
    ])

    {{-- ═══════ Organisation (partial complet 8 sections) ═══════ --}}
    @include('partials._organisation', [
        'pageOrganisation' => $pageOrganisation,
        'settings'         => $settings,
        'founder'          => $founder,
        'yearsOfActivity'  => $yearsOfActivity,
        'totalProjects'    => $totalProjects,
        'domainCount'      => $domainCount,
        'timeline'         => $timeline,
        'recognitions'     => $recognitions,
        'zones'            => $zones,
        'locale'           => $locale,
    ])

@stop

{{-- ─── Scripts spécifiques ─────────────────────────────────────── --}}
@push('scripts')
    <script src="{{ asset('js/oaat.js') }}"></script>
@endpush
