# Assemble une nouvelle vue domaines-index fondée sur le layout, sans rien casser du contenu.
$ErrorActionPreference = 'Stop'
$p   = 'c:/laragon/www/oaat/resources/views/pages/domaines-index.blade.php'
$l   = Get-Content -LiteralPath $p   # 0-based

# Bloc 1 : @php domaine + rail (lignes 42..201) SANS l'ouverture (41) ni $liensNav ni @endphp (215)
$c1 = for ($i = 41; $i -le 200; $i++) { $l[$i] }
# Bloc 2 : @php projets (lignes 218..254) SANS ouverture (217) ni @endphp (255)
$c2 = for ($i = 217; $i -le 253; $i++) { $l[$i] }
# Corps main (lignes 348..583) : commentaire héro -> bandeau CTA
$corps = for ($i = 347; $i -le 582; $i++) { $l[$i] }

$nl = [Environment]::NewLine
$out = [System.Collections.Generic.List[string]]::new()

$header = @(
'{{-- pages/domaines-index.blade.php — « Domaines d''intervention ».
     Vue intégrée au layout commun : topbar, en-tête et pied de page proviennent
     de layouts/app.blade.php. Contenu rendu côté serveur (Blade), sans injection
     HTML ni traduction par JavaScript. --}}'
"@extends('layouts.app')"
''
"@php"
'    // $t et $locale doivent être définis avant toute @section qui les utilise (cf. projets-index).'
"    `$locale ??= 'fr';"
"    `$t = fn(`$fr, `$en) => `$locale === 'en' ? `$en : `$fr;"
'@endphp'
''
"@section('meta_title', `$t(\"Domaines d'intervention — OAAT\", 'Areas of intervention — OAAT'))"
"@section('meta_description', `$t("
"    \"Les neuf domaines d'intervention de l'OAAT : santé, sécurité alimentaire, logistique, éducation, eau et assainissement, genre, protection, environnement et formation aux métiers.\","
"    'The nine areas of intervention of OAAT: health, food security, logistics, education, water and sanitation, gender, protection, environment and vocational training.',"
'))'
"@push('head')"
'    <style>'
'        .animate-dash { animation: dash 5s ease-out forwards; }'
'        .motion-reduce .animate-dash { animation: none; }'
'    </style>'
'@endpush'
''
'@php'
'@endphp'
''
'@php'
'@endphp'
''
"@section('content')"
''
)

$outerContent = @(
'{{-- ═══════════════════════════════════════════════════════════════════════
    H É R O
═══════════════════════════════════════════════════════════════════════ --}}'
)

foreach ($h in $header) { $out.Add($h) }
# Bloc 1 emballé dans son @php
$out.Add('@php')
foreach ($x in $c1) { $out.Add($x) }
$out.Add('@endphp')
# Bloc 2 emballé dans son @php
$out.Add('@php')
foreach ($x in $c2) { $out.Add($x) }
$out.Add('@endphp')
$out.Add('')
# Corps
foreach ($x in $corps) { $out.Add($x) }
$out.Add('')
$out.Add("@endsection")
$out.Add('')
$out.Add("@push('scripts')")
$out.Add("    <script src=\"{{ asset('js/oaat.js') }}\" defer></script>")
$out.Add('@endpush')

$text = ($out -join $nl) + $nl
[System.IO.File]::WriteAllText($p, $text, (New-Object System.Text.UTF8Encoding($false)))
Write-Output ('OK lignes=' + $out.Count)
