{{-- ═══════════════════════════════════════════════════════════════════
    _logo_oaat.blade.php — Logo OAAT en SVG (source unique).
    Variables : $svgClass (classes de taille, défaut « h-11 w-11 »). Le logo
    figure dans l'en-tête, le pied de page et le bloc « mise en œuvre ».
    --}}
<svg viewBox="0 0 40 40" class="{{ $svgClass ?? 'h-11 w-11' }}" aria-hidden="true">
    <rect width="40" height="40" rx="9" fill="#17508F"/>
    <path d="M5 27c6-9 11 3 17-6s8-5 13-9M5 21c5-8 10 2 15-5s9-4 15-8M5 33c7-8 12 3 18-5s7-4 12-7"
          fill="none" stroke="#E3A82B" stroke-width="2" stroke-linecap="round"/>
</svg>
