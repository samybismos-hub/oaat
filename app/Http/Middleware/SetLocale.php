<?php

namespace App\Http\Middleware;

use App\App;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

/**
 * SetLocale — Middleware de routage bilingue.
 *
 * Lis le segment {locale?} dans l'URL (fr ou en),
 * active la traduction correspondante via App::setLocale(),
 * et partage la locale avec toutes les vues Blade.
 *
 * Si l'URL est "/" (sans préfixe), redirige vers "/fr/".
 */
class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // 1. Lire la locale depuis le paramètre de route {locale?}
        $locale = $request->route('locale') ?? 'fr';

        // 2. Normaliser en minuscules
        $locale = Str::lower($locale);

        // 3. Sécurité : si la locale n'est ni fr ni en, forcer 'fr'
        if (! in_array($locale, ['fr', 'en'])) {
            $locale = 'fr';
        }

        // 4. Activer la locale dans Laravel (traductions, dates, etc.)
        App::setLocale($locale);

        // 5. Partager la locale avec toutes les vues Blade
        view()->share('locale', $locale);

        // 6. Si l'URL est "/" (pas de préfixe), rediriger vers "/fr/" (ou "/en/")
        //    pour éviter le contenu dupliqué (SEO).
        if ($request->path() === '/') {
            return Redirect::to("/{$locale}/");
        }

        // 7. Continuer la chaîne vers le contrôleur
        return $next($request);
    }
}