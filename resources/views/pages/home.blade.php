{{--
    pages/home.blade.php — Page d'accueil complète.
    ────────────────────────────────────────────────────────────────
    Hérite de layouts.app (topbar, header, footer, toast).
    Assemble les 8 sections principales via @include.
    Variables disponibles (depuis AccueilController) :
        $settings, $domaines, $projets, $projetPhare, $tousProjets,
        $totalBeneficiaires, $actualites, $partenaires, $equipe,
        $organisation, $pageOrganisation, $timeline, $recognitions,
        $yearsOfActivity, $locale
--}}
@extends('layouts.app')

{{-- ─── SEO : titre & description dynamiques ─────────────────── --}}
@section('meta_title')
    {{ $organisation?->seoTitle($locale) ?: "OAAT — Organisation Africaine pour l'Aménagement des Territoires" }}
@endsection

@section('meta_description')
    {{ $organisation?->seoDescription($locale) ?: "ONG interafricaine active à l'Est de la RD Congo depuis 1995 : santé, eau, éducation, sécurité alimentaire, infrastructures, protection et environnement." }}
@endsection

@section('content')

    {{-- ═══════ HERO : carrousel 3 diapos ═══════════════════════ --}}
    @include('partials._hero', ['locale' => $locale])

    {{-- ═══════ CHIFFRES CLÉS ═══════════════════════════════════ --}}
    @include('partials._chiffres', [
        'settings'          => $settings,
        'domaines'          => $domaines,
        'tousProjets'       => $tousProjets,
        'yearsOfActivity'   => $yearsOfActivity,
        'locale'            => $locale,
    ])

    {{-- ═══════ MISSION / ORGANISATION ══════════════════════════ --}}
    @include('partials._about', [
        'organisation'      => $organisation,
        'timeline'          => $timeline,
        'recognitions'      => $recognitions,
        'locale'            => $locale,
    ])

    {{-- ═══════ DOMAINES D'INTERVENTION ═════════════════════════ --}}
    @include('partials._domaines', [
        'domaines'          => $domaines,
        'locale'            => $locale,
    ])

    {{-- ═══════ PROJETS ═════════════════════════════════════════ --}}
    @include('partials._projets', [
        'projets'           => $projets,
        'projetPhare'       => $projetPhare,
        'locale'            => $locale,
    ])

    {{-- ═══════ ACTUALITÉS ══════════════════════════════════════ --}}
    @include('partials._actualites', [
        'actualites'        => $actualites,
        'locale'            => $locale,
    ])

    {{-- ═══════ PARTENAIRES ═════════════════════════════════════ --}}
    @include('partials._partenaires', [
        'partenaires'       => $partenaires,
        'locale'            => $locale,
    ])

    {{-- ═══════ CONTACT ═════════════════════════════════════════ --}}
    @include('partials._contact', [
        'settings'          => $settings,
        'locale'            => $locale,
    ])

@stop