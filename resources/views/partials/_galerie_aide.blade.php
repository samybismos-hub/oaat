{{--
    _galerie_aide.blade.php — Modal « Raccourcis clavier ».
    ────────────────────────────────────────────────────────────────────────
    Ouverte par #hlp (héro) ou par la touche « ? » (§23.10), fermée par #hx
    ou « Échap ». Affiche la liste des raccourcis de la visionneuse et de la
    page. Variables : $locale, $t.
--}}
<div id="help" class="fixed inset-0 z-[95] hidden items-center justify-center bg-ink/70 p-4" role="dialog" aria-modal="true" aria-label="{{ $t('Raccourcis clavier', 'Keyboard shortcuts') }}">
    <div class="w-full max-w-lg bg-white p-8">
        <div class="flex items-center justify-between">
            <h2 class="font-serif text-2xl font-semibold text-ink">{{ $t('Raccourcis clavier', 'Keyboard shortcuts') }}</h2>
            <button type="button" id="hx" class="h-10 w-10 border border-line" aria-label="{{ $t('Fermer', 'Close') }}">✕</button>
        </div>

        <dl class="mt-6 divide-y divide-line text-sm">
            @php
                $raccourcis = [
                    ['/',  $t('Rechercher', 'Search')],
                    ['G',  $t('Changer de vue', 'Change view')],
                    ['F',  $t('Ouvrir les filtres', 'Open filters')],
                    ['← →', $t('Image précédente / suivante', 'Previous / next image')],
                    ['+ −  0', $t('Zoom avant / arrière / réinitialiser', 'Zoom in / out / reset')],
                    [$t('Espace', 'Space'), $t('Diaporama', 'Slideshow')],
                    ['I',  $t('Informations', 'Information')],
                    ['S',  $t('Sélectionner l’image', 'Select the image')],
                    ['C',  $t('Copier le lien', 'Copy link')],
                    ['F11 / ⛶', $t('Plein écran', 'Full screen')],
                    [$t('Échap', 'Esc'), $t('Fermer', 'Close')],
                ];
            @endphp
            @foreach ($raccourcis as [$touche, $aide])
                <div class="flex items-center justify-between gap-4 py-2.5">
                    <dt>
                        <kbd class="border border-line bg-mist px-2 py-0.5 text-xs font-semibold">{{ $touche }}</kbd>
                    </dt>
                    <dd class="text-ink/75">{{ $aide }}</dd>
                </div>
            @endforeach
        </dl>
    </div>
</div>