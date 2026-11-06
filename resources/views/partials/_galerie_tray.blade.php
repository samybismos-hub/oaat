{{--
    _galerie_tray.blade.php — Dock de sélection (bas d'écran).
    ────────────────────────────────────────────────────────────────────────
    Affiché/masqué par le JS §23.5 (translate-y-full). #tn = compteur,
    #tl = références, #tall/#tinv/#tcp/#tclr = actions, #treq = demande par
    email dont le href est construit par le JS (au rendu serveur : « # »).
--}}
<div id="tray" class="fixed inset-x-0 bottom-0 z-[60] translate-y-full border-t border-line bg-white px-5 py-3 shadow-[0_-12px_30px_-12px_rgba(11,37,69,.25)] transition duration-300 print:hidden">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-x-6 gap-y-2 pr-16 lg:px-3">
        <p class="text-sm font-semibold text-ink">
            <span id="tn">0</span>
            {{ $t('sélectionnée(s)', 'selected') }}
        </p>
        <p id="tl" class="hidden min-w-0 max-w-md truncate text-sm text-ink/55 md:block"></p>

        <div class="ml-auto flex flex-wrap items-center gap-2 text-sm">
            <button type="button" id="tall" class="border border-line px-3 py-2 transition hover:border-lake hover:text-lake">{{ $t('Tout sélectionner', 'Select all results') }}</button>
            <button type="button" id="tinv" class="border border-line px-3 py-2 transition hover:border-lake hover:text-lake">{{ $t('Inverser', 'Invert') }}</button>
            <button type="button" id="tcp" class="border border-line px-3 py-2 transition hover:border-lake hover:text-lake">{{ $t('Copier les références', 'Copy references') }}</button>
            <button type="button" id="tclr" class="border border-line px-3 py-2 transition hover:border-lake hover:text-lake">{{ $t('Désélectionner', 'Deselect all') }}</button>
            <a id="treq" href="#" class="bg-lake px-4 py-2 font-semibold text-white transition hover:bg-deep">{{ $t('Demander ces images', 'Request these images') }}</a>
        </div>
    </div>
</div>