{{--
    _galerie_visionneuse.blade.php — Visionneuse plein écran (#vw).
    ────────────────────────────────────────────────────────────────────────
    Ouverte par .open / #sl0 / lien direct #gN (§23.9). Colonne image : barre
    supérieure (#vbar), scène zoomable (#vst + #vi), navigation (#vp/#vn),
    progression du diaporama (#vpr), filmstrip (#vfl + .fs). Panneau infos :
    #vin (#vcat, #vt, #vm1-10, .vtg, boutons #vs/#vd/#vc/#vsh/#vq/#vsim/#vsrc).
    Variables : $medias, $tags (vocabulaire .vtg), $locale, $t.
--}}
<div id="vw" class="fixed inset-0 z-[90] hidden bg-ink text-white print:hidden" role="dialog" aria-modal="true" aria-label="{{ $t('Visionneuse', 'Viewer') }}">
    <div class="flex h-full flex-col lg:flex-row">

        {{-- ── Colonne image --}}
        <div class="relative flex min-h-0 grow flex-col">

            {{-- Barre supérieure : compteur + zoom / diaporama / plein écran / infos / fermer --}}
            <div id="vbar" class="absolute inset-x-0 top-0 z-20 flex items-center justify-between gap-3 bg-gradient-to-b from-black/70 to-transparent px-4 py-3 transition-opacity duration-300">
                <span id="vcn" class="tabular-nums text-sm text-white/80"></span>
                <div class="flex items-center gap-1.5">
                    <button type="button" id="vzo"
                            class="grid h-10 w-10 place-items-center border border-white/25 text-white transition hover:bg-white hover:text-ink"
                            aria-label="{{ $t('Zoom arrière', 'Zoom out') }}" title="{{ $t('Zoom arrière', 'Zoom out') }}">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/></svg>
                    </button>
                    <button type="button" id="vzi"
                            class="grid h-10 w-10 place-items-center border border-white/25 text-white transition hover:bg-white hover:text-ink"
                            aria-label="{{ $t('Zoom avant', 'Zoom in') }}" title="{{ $t('Zoom avant', 'Zoom in') }}">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    </button>
                    <button type="button" id="vz0"
                            class="grid h-10 w-10 place-items-center border border-white/25 text-xs font-semibold text-white transition hover:bg-white hover:text-ink"
                            aria-label="{{ $t('Taille d’origine', 'Original size') }}" title="{{ $t('Taille d’origine', 'Original size') }}">1:1</button>
                    <button type="button" id="vsl"
                            class="grid h-10 w-10 place-items-center border border-white/25 text-white transition hover:bg-white hover:text-ink"
                            aria-label="{{ $t('Diaporama', 'Slideshow') }}" title="{{ $t('Diaporama', 'Slideshow') }}">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 5l12 7-12 7z"/></svg>
                    </button>
                    <button type="button" id="vfs"
                            class="grid h-10 w-10 place-items-center border border-white/25 text-white transition hover:bg-white hover:text-ink"
                            aria-label="{{ $t('Plein écran', 'Full screen') }}" title="{{ $t('Plein écran', 'Full screen') }}">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 3H3v5M16 3h5v5M8 21H3v-5M16 21h5v-5"/></svg>
                    </button>
                    <button type="button" id="vii"
                            class="grid h-10 w-10 place-items-center border border-white/25 text-white transition hover:bg-white hover:text-ink"
                            aria-label="{{ $t('Informations', 'Information') }}" title="{{ $t('Informations', 'Information') }}">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 8h.01M11 12h1v5h1M12 22a10 10 0 100-20 10 10 0 000 20z"/></svg>
                    </button>
                    <button type="button" id="vx"
                            class="grid h-10 w-10 place-items-center border border-white/25 text-white transition hover:bg-white hover:text-ink"
                            aria-label="{{ $t('Fermer', 'Close') }}" title="{{ $t('Fermer', 'Close') }}">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                    </button>
                </div>
            </div>

            {{-- Scène : image zoomable au pointeur / pincement + navigation --}}
            <div id="vst" class="relative min-h-0 grow touch-none select-none overflow-hidden">
                <img id="vi" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                     alt="" draggable="false" class="absolute inset-0 h-full w-full object-contain will-change-transform">
                <button type="button" id="vp"
                        class="absolute left-3 top-1/2 z-10 grid h-10 w-10 -translate-y-1/2 place-items-center border border-white/25 bg-ink/40 text-white transition hover:bg-white hover:text-ink"
                        aria-label="{{ $t('Précédente', 'Previous') }}">‹</button>
                <button type="button" id="vn"
                        class="absolute right-3 top-1/2 z-10 grid h-10 w-10 -translate-y-1/2 place-items-center border border-white/25 bg-ink/40 text-white transition hover:bg-white hover:text-ink"
                        aria-label="{{ $t('Suivante', 'Next') }}">›</button>
            </div>

            {{-- Progression du diaporama (pilotée par setPlay, §23.9) --}}
            <div class="h-0.5 bg-white/10">
                <div id="vpr" class="h-full w-0 bg-ochre"></div>
            </div>

            {{-- Filmstrip : retour direct à une photo (une vignette .fs par média) --}}
            <div id="vfl" class="flex gap-2 overflow-x-auto bg-black/40 px-3 py-2">
                @foreach ($medias as $m)
                    <button type="button" class="fs hidden shrink-0 opacity-60 outline-offset-2 transition hover:opacity-100"
                            data-id="{{ $m['id'] ?? 0 }}" aria-label="Photo {{ $m['id'] ?? 0 }}">
                        <img src="{{ $m['thumb'] ?? '' }}" alt="" class="h-14 w-[4.5rem] object-cover">
                    </button>
                @endforeach
            </div>
        </div>

        {{-- ── Panneau d'informations --}}
        <aside id="vin" class="max-h-[42vh] w-full shrink-0 overflow-y-auto border-t border-white/10 bg-[#0d1f35] p-6 lg:max-h-none lg:w-96 lg:border-l lg:border-t-0">
            {{-- Catégorie + titre (remplis par updV, §23.9) --}}
            <p id="vcat" class="text-xs font-semibold uppercase tracking-[.18em] text-ochre"></p>
            <h2 id="vt" class="mt-3 font-serif text-2xl font-semibold leading-snug"></h2>

            {{-- Fiche technique (libellés fixes, valeurs #vm1-10 remplies par le JS) --}}
            <dl class="mt-6 divide-y divide-white/10 text-sm">
                @foreach ([
                    ['Référence', 'Reference'],
                    ['Année', 'Year'],
                    ['Thème', 'Theme'],
                    ['Lieu', 'Location'],
                    ['Partenaires', 'Partners'],
                    ['Collections', 'Collections'],
                    ['Orientation', 'Orientation'],
                    ['Dimensions', 'Dimensions'],
                    ['Utilisée dans', 'Used in'],
                    ['Crédit', 'Credit'],
                ] as $row)
                    <div class="flex justify-between gap-4 py-2">
                        <dt class="text-white/55">{{ $t($row[0], $row[1]) }}</dt>
                        <dd id="vm{{ $loop->iteration }}" class="text-right font-medium"></dd>
                    </div>
                @endforeach
            </dl>

            {{-- Mots-clés : une puce .vtg par tag (affichées selon l'image courante) --}}
            @if ($tags->isNotEmpty())
                <div class="mt-5">
                    <p class="text-xs uppercase tracking-wider text-white/55">{{ $t('Mots-clés', 'Keywords') }}</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($tags as $tag)
                            <button type="button" class="vtg hidden border border-white/25 px-2.5 py-1 text-xs text-white/80 transition hover:bg-white hover:text-ink"
                                    data-tg="{{ $tag['slug'] ?? ($tag['fr'] ?? '') }}">
                                {{ $tag['fr'] ?? '' }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Actions : sélection, téléchargement, copie, partage, autorisation, similaire, source --}}
            <div class="mt-6 grid gap-2 text-sm">
                <button type="button" id="vs" class="border border-white/30 px-4 py-3 text-left font-semibold transition hover:bg-white hover:text-ink" aria-pressed="false">
                    <span class="v-off">{{ $t('Ajouter à ma sélection', 'Add to my selection') }}</span>
                    <span class="v-on hidden">{{ $t('Retirer de ma sélection', 'Remove from my selection') }}</span>
                </button>
                <a id="vd" href="#" download class="border border-white/30 px-4 py-3 transition hover:bg-white hover:text-ink">{{ $t('Télécharger l’image', 'Download the image') }}</a>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" id="vc" class="border border-white/30 px-4 py-3 transition hover:bg-white hover:text-ink">{{ $t('Copier le lien', 'Copy link') }}</button>
                    <button type="button" id="vsh" class="border border-white/30 px-4 py-3 transition hover:bg-white hover:text-ink">{{ $t('Partager', 'Share') }}</button>
                </div>
                <a id="vq" href="#" class="border border-white/30 px-4 py-3 transition hover:bg-white hover:text-ink">{{ $t('Demander l’autorisation d’utilisation', 'Request permission to use') }}</a>
                <button type="button" id="vsim" class="border border-white/30 px-4 py-3 text-left transition hover:bg-white hover:text-ink">{{ $t('Voir les images similaires', 'See similar images') }}</button>
                <a id="vsrc" href="#" class="px-1 py-2 font-semibold text-ochre underline-offset-4 hover:underline"><span id="vsrt"></span> →</a>
            </div>
        </aside>
    </div>
</div>