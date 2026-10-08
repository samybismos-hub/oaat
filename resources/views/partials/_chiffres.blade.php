{{--
    _chiffres.blade.php — Barre de statistiques clés (5 chiffres).
    ────────────────────────────────────────────────────────────────
    Variables disponibles :
        $yearsOfActivity  (int)    — années d'activité
        $tousProjets      (Collection) — tous les projets publiés
        $domaines         (Collection) — tous les domaines
        $settings         (Setting|null) — paramètres du site
        $locale           (string)
        $t                (helper) — traduction statique
--}}
<section class="relative z-30 mx-auto -mt-14 max-w-6xl px-5">
    <div class="grid grid-cols-2 divide-line bg-white p-2 shadow-2xl ring-1 ring-line md:grid-cols-5 md:divide-x" id="stats">

        {{-- ═══ Stat 1 : Années d'action ═══════════════════════ --}}
        <div class="p-5 text-center">
            <div class="font-serif text-4xl font-semibold text-lake md:text-5xl">
                <span data-n="{{ $yearsOfActivity ?? 31 }}">0</span>
            </div>
            <p class="mt-1 text-sm text-ink/65">
                {{ $t("ans d'action depuis 1995", 'years of action since 1995') }}
            </p>
        </div>

        {{-- ═══ Stat 2 : Projets réalisés ═══════════════════════ --}}
        <div class="p-5 text-center">
            <div class="font-serif text-4xl font-semibold text-lake md:text-5xl">
                <span data-n="{{ count($tousProjets ?? []) }}">0</span>
            </div>
            <p class="mt-1 text-sm text-ink/65">
                {{ $t('projets réalisés', 'projects completed') }}
            </p>
        </div>

        {{-- ═══ Stat 3 : Domaines d'intervention ═══════════════ --}}
        <div class="p-5 text-center">
            <div class="font-serif text-4xl font-semibold text-lake md:text-5xl">
                <span data-n="{{ count($domaines ?? []) }}">0</span>
            </div>
            <p class="mt-1 text-sm text-ink/65">
                {{ $t("domaines d'intervention", 'areas of intervention') }}
            </p>
        </div>

        {{-- ═══ Stat 4 : Zones d'intervention ══════════════════ --}}
        <div class="p-5 text-center">
            <div class="font-serif text-4xl font-semibold text-lake md:text-5xl">
                <span data-n="{{ $settings?->stat_zones ?? 13 }}">0</span>
            </div>
            <p class="mt-1 text-sm text-ink/65">
                {{ $t('zones en Sud et Nord-Kivu', 'zones in South and North Kivu') }}
            </p>
        </div>

        {{-- ═══ Stat 5 : Projets prêts à financer (colspanée mobile) ═══ --}}
        <div class="p-5 text-center col-span-2 md:col-span-1">
            <div class="font-serif text-4xl font-semibold text-lake md:text-5xl">
                <span data-n="{{ $settings?->stat_projects ?? 6 }}">0</span>
            </div>
            <p class="mt-1 text-sm text-ink/65">
                {{ $t('projets prêts à financer', 'projects ready to fund') }}
            </p>
        </div>

    </div>
</section>