{{--
    _galerie_grille.blade.php — Grille des médias + palette bas de grille.
    ────────────────────────────────────────────────────────────────────────
    #gr contient les <figure class="ph"> servies par _galerie_media. Le JS §23
    filtre, trie, positionne (mosaïque/grille/liste) et gère la pagination :
    la vue n'a qu'à les servir. #em = état vide, #more/#pg2/#snt = pagination.
    Variables : $medias, $locale, $t.
--}}
<div id="gr" data-view="masonry" class="group/g relative">
    @foreach ($medias as $m)
        @include('partials._galerie_media', ['m' => $m])
    @endforeach
</div>

{{-- État vide (le JS le masque dès qu'un média est visible ; sans données la
     grille est vide et l'état vide doit rester affiché) --}}
<div id="em" class="{{ $medias->isEmpty() ? '' : 'hidden' }} border border-dashed border-line px-6 py-16 text-center">
    <p class="font-serif text-2xl text-ink">{{ $t('Aucun média ne correspond à ces critères.', 'No media matches these criteria.') }}</p>
    <p class="mt-2 text-ink/60">{{ $t('Essayez de retirer un filtre ou de modifier votre recherche.', 'Try removing a filter or changing your search.') }}</p>
    <button type="button" class="emc mt-6 bg-lake px-6 py-3 font-semibold text-white transition hover:bg-deep">
        {{ $t('Tout effacer', 'Clear everything') }}
    </button>
</div>

{{-- Pagination : compteur (#pg2), bouton « afficher plus » (#more), sentinelle
     de défilement (#snt) — le tout piloté par §23.6 --}}
<div class="mt-10 flex flex-col items-center gap-3">
    <p id="pg2" class="text-sm text-ink/55"></p>
    <button type="button" id="more" class="hidden border border-lake px-8 py-3 font-semibold text-lake transition hover:bg-lake hover:text-white">
        {{ $t('Afficher plus', 'Show more') }}
    </button>
    <div id="snt" class="h-px w-full"></div>
</div>