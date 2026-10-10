{{-- _organisation.blade.php --}}
{{-- HERO --}}
<section id="top" class="relative overflow-hidden bg-gradient-to-br from-deep via-deep to-[#123d6e] text-white">
<svg class="pointer-events-none absolute inset-0 z-0 h-full w-full opacity-20" viewBox="0 0 1440 800" preserveAspectRatio="none" fill="none" stroke="#E3A82B" stroke-width="1.3" aria-hidden="true">
<path pathLength="1" stroke-dasharray="1" stroke-dashoffset="1" class="animate-dash motion-reduce:animate-none" d="M-20 600C220 520 380 690 640 600S1060 470 1460 560"/>
<path pathLength="1" stroke-dasharray="1" stroke-dashoffset="1" class="animate-dash motion-reduce:animate-none" style="animation-delay:.5s" d="M-20 650C240 580 400 730 680 650S1080 530 1460 610"/>
<path pathLength="1" stroke-dasharray="1" stroke-dashoffset="1" class="animate-dash motion-reduce:animate-none" style="animation-delay:1s" d="M-20 700C260 640 420 770 700 700S1100 590 1460 660"/>
<path pathLength="1" stroke-dasharray="1" stroke-dashoffset="1" class="animate-dash motion-reduce:animate-none" style="animation-delay:1.5s" d="M-20 550C200 470 360 640 620 550S1040 420 1460 510"/>
</svg>
<div class="relative z-10 mx-auto max-w-7xl px-5 py-16 lg:px-8 lg:py-24">
<h1 class="font-serif text-5xl font-semibold leading-tight md:text-6xl" data-r>{{ $t('Organisation','Organisation') }}</h1>
<p class="mt-5 max-w-2xl text-lg text-white/75" data-r>{{ $t("Qui nous sommes, d'ou nous venons et la maniere dont nous travaillons aux cotes des communautes de l'Est de la RD Congo.","Who we are, where we come from and how we work alongside communities in eastern DR Congo.") }}</p>
<dl class="mt-12 grid max-w-3xl grid-cols-3 gap-4 border-t border-white/15 pt-8 sm:gap-8">
<div data-r><dd class="font-serif text-4xl font-semibold"><span data-n="{{ $yearsOfActivity }}">0</span></dd><dt class="mt-1 text-sm text-white/65">{{ $t("ans d'action depuis 1995","years of action since 1995") }}</dt></div>
<div data-r><dd class="font-serif text-4xl font-semibold"><span data-n="{{ $totalProjects }}">0</span></dd><dt class="mt-1 text-sm text-white/65">{{ $t("projets realises","projects completed") }}</dt></div>
<div data-r><dd class="font-serif text-4xl font-semibold"><span data-n="{{ $domainCount }}">0</span></dd><dt class="mt-1 text-sm text-white/65">{{ $t("domaines d'intervention","areas of intervention") }}</dt></div>
</dl></div></section>
{{-- SOUS-NAV --}}
<div class="sticky top-[60px] z-30 border-b border-line bg-white/95 backdrop-blur"><nav id="sn" class="mx-auto flex max-w-7xl overflow-x-auto px-5 lg:px-8" aria-label="{{ $t('Sections','Sections') }}">
<a href="#presentation" class="snl whitespace-nowrap border-b-2 px-4 py-3.5 text-sm font-medium transition border-ochre text-lake">{{ $t('Présentation','Overview') }}</a>
<a href="#mission" class="snl whitespace-nowrap border-b-2 px-4 py-3.5 text-sm font-medium transition border-transparent text-ink/70 hover:text-lake">{{ $t('Mission et vision','Mission and vision') }}</a>
<a href="#histoire" class="snl whitespace-nowrap border-b-2 px-4 py-3.5 text-sm font-medium transition border-transparent text-ink/70 hover:text-lake">{{ $t('Histoire','History') }}</a>
<a href="#fondateur" class="snl whitespace-nowrap border-b-2 px-4 py-3.5 text-sm font-medium transition border-transparent text-ink/70 hover:text-lake">{{ $t('Fondateur','Founder') }}</a>
<a href="#reconnaissance" class="snl whitespace-nowrap border-b-2 px-4 py-3.5 text-sm font-medium transition border-transparent text-ink/70 hover:text-lake">{{ $t('Reconnaissance','Recognition') }}</a>
<a href="#zones" class="snl whitespace-nowrap border-b-2 px-4 py-3.5 text-sm font-medium transition border-transparent text-ink/70 hover:text-lake">{{ $t('Zones','Areas') }}</a>
</nav></div>
{{-- PRESENTATION --}}
<section id="presentation" class="mx-auto max-w-7xl scroll-mt-32 px-5 py-24 lg:px-8">
<div class="grid gap-14 lg:grid-cols-5">
<div class="lg:col-span-3" data-r>
<h2 class="font-serif text-4xl font-semibold leading-tight text-lake md:text-5xl">{{ $t("Une organisation nee du besoin de reconstruire","An organisation born from the need to rebuild") }}</h2>
<p class="mt-6 text-lg leading-relaxed text-ink/75">{{ $t("L'OAAT est une organisation sans but lucratif qui intervient a l'Est de la RD Congo depuis 1995. Apres une dizaine d'annees de conflits armes, la region a sombre dans l'instabilite socio-economique et culturelle, avec de lourdes consequences pour les communautes et leurs infrastructures.","OAAT is a non-profit organisation working in eastern DR Congo since 1995. After about ten years of armed conflict, the region sank into socio-economic and cultural instability, with severe consequences for communities and their infrastructure.") }}</p>
<p class="mt-4 leading-relaxed text-ink/75">{{ $t("Un groupe d'acteurs soucieux de l'avenir de la region y mene des reflexions avec la societe civile du Sud-Kivu et les membres des clusters. Leur question : quelle approche pour un processus de paix inclusif, qui ameliore les infrastructures et facilite la mobilite des personnes et de leurs biens ?","A group of committed actors reflects with South Kivu civil society and cluster members. Their question: what approach can deliver an inclusive peace process that improves infrastructure and eases the movement of people and goods?") }}</p>
<ul class="mt-8 grid gap-4 sm:grid-cols-2">
<li class="border-l-4 border-ochre bg-mist p-5 text-sm leading-relaxed">{{ $t("Impulser la resilience dans les communautes beneficiaires, pour qu'elles s'approprient les resultats et les capitalisent en developpement durable.","Foster resilience in beneficiary communities so they own the results and turn them into lasting development.") }}</li>
<li class="border-l-4 border-ochre bg-mist p-5 text-sm leading-relaxed">{{ $t("Batir avec les communautes des strategies de stabilite socio-economique et culturelle et de paix durable dans la region des Grands Lacs.","Build with communities strategies for socio-economic and cultural stability and lasting peace in the Great Lakes region.") }}</li>
</ul></div>
<dl data-r class="h-fit border border-line border-t-4 border-t-lake bg-white p-6 shadow-xl lg:col-span-2">
<div class="flex gap-4 border-b border-line py-4 last:border-0"><dt class="w-32 shrink-0 text-sm text-ink/60">{{ $t("Creation","Founded") }}</dt><dd class="font-semibold">{{ $t("10 mai 1995","10 May 1995") }}</dd></div>
<div class="flex gap-4 border-b border-line py-4 last:border-0"><dt class="w-32 shrink-0 text-sm text-ink/60">{{ $t("Statut","Status") }}</dt><dd class="font-semibold">{{ $t("ONG interafricaine, apolitique et non confessionnelle, sans but lucratif","Pan-African NGO, apolitical, non-denominational and non-profit") }}</dd></div>
<div class="flex gap-4 border-b border-line py-4 last:border-0"><dt class="w-32 shrink-0 text-sm text-ink/60">{{ $t("Zone d'action","Area of action") }}</dt><dd class="font-semibold">{{ $t("Est de la RD Congo : Sud-Kivu et Nord-Kivu","Eastern DR Congo: South Kivu and North Kivu") }}</dd></div>
<div class="flex gap-4 border-b border-line py-4 last:border-0"><dt class="w-32 shrink-0 text-sm text-ink/60">{{ $t("Siege social","Head office") }}</dt><dd class="font-semibold">{{ $t("Nyangezi, territoire de Walungu","Nyangezi, Walungu territory") }}</dd></div>
<div class="flex gap-4 border-b border-line py-4 last:border-0"><dt class="w-32 shrink-0 text-sm text-ink/60">{{ $t("Representation","Representation") }}</dt><dd class="font-semibold">{{ $t("Bukavu, Sud-Kivu","Bukavu, South Kivu") }}</dd></div>
</dl></div></section>
{{-- VISION & MISSION --}}
<section id="mission" class="scroll-mt-32 bg-mist py-24">
<div class="mx-auto max-w-7xl px-5 lg:px-8"><div class="grid gap-6 lg:grid-cols-2">
<article data-r class="border border-line border-t-4 border-t-lake bg-white p-8 lg:p-10">
<h2 class="font-serif text-3xl font-semibold text-lake">{{ $t("Vision","Vision") }}</h2>
<p class="mt-4 leading-relaxed text-ink/75">{{ $t("Renouer le lien entre le developpement technologique et les besoins exprimes par les populations les plus defavorisees de la RD Congo, par l'amenagement des territoires urbains et ruraux et la realisation de projets sociaux.","Reconnect technological development with the needs expressed by the most disadvantaged people in DR Congo, through urban and rural land planning and social projects.") }}</p>
</article>
<article data-r class="border border-line border-t-4 border-t-ochre bg-white p-8 lg:p-10">
<h2 class="font-serif text-3xl font-semibold text-lake">{{ $t("Mission","Mission") }}</h2>
<p class="mt-4 leading-relaxed text-ink/75">{{ $t("Apporter notre savoir-faire aux personnes, ONG et entreprises de notre rayon d'action pour promouvoir le developpement socio-economique et technique : agriculture, sante, education, eau, hygiene et assainissement, protection, genre, environnement et reinsertion des personnes vulnerables, deplacees, retournees ou handicapees.","Bring our know-how to people, NGOs and businesses in our area of action to promote socio-economic and technical development: agriculture, health, education, water, sanitation and hygiene, protection, gender, environment and the reintegration of vulnerable, displaced, returning or disabled people.") }}</p>
</article>
</div></div></section>
{{-- TIMELINE --}}
<section id="histoire" class="mx-auto max-w-7xl scroll-mt-32 px-5 py-24 lg:px-8">
<div class="relative">
<div id="tl" class="absolute bottom-0 left-[19px] top-0 w-[3px] bg-line md:left-1/2 md:-ml-[1.5px]" aria-hidden="true"><div id="tlp" class="h-0 w-full bg-ochre transition-[height] duration-200 ease-out"></div></div>
<ol class="relative">
@forelse ($timeline as $entry)
@php [$year, $title, $desc] = $entry; @endphp
<li data-r class="relative pb-12 pl-10 last:pb-0 md:grid md:grid-cols-2 md:gap-16 md:pl-0">
<i class="absolute left-0 top-2 z-10 h-4 w-4 rounded-full border-2 border-ochre bg-white md:left-1/2 md:-ml-2"></i>
<p class="font-serif text-4xl font-semibold text-lake md:text-right">{{ $year }}</p>
<div><h3 class="font-serif text-xl font-semibold">{{ $title }}</h3><p class="mt-2 leading-relaxed text-ink/70">{{ $desc }}</p></div>
</li>
@empty
<li class="col-span-2 text-center text-sm text-ink/40 italic">{{ $t("Aucun jalon historique pour le moment.","No historical milestones yet.") }}</li>
@endforelse
</ol>
</div></section>
{{-- FONDATEUR --}}
<section id="fondateur" class="scroll-mt-32 bg-mist py-24">
<div class="mx-auto grid max-w-7xl items-center gap-14 px-5 lg:grid-cols-2 lg:px-8">
<div data-r class="overflow-hidden shadow-xl">
@if ($founder?->getFirstMediaUrl('photo', 'card'))
<img src="{{ $founder->getFirstMediaUrl('photo', 'card') }}" alt="{{ $founder?->name ?? $t('Portrait du fondateur','Portrait of the founder') }}" data-w class="aspect-[4/5] w-full object-cover">
@else
<img data-w data-ph="Portrait du fondateur|205" alt="{{ $t('Portrait de Roger Manema Cirhahingirwa','Portrait of Roger Manema Cirhahingirwa') }}" class="aspect-[4/5] w-full object-cover">
@endif
</div>
<div data-r>
<h2 class="font-serif text-4xl font-semibold text-lake md:text-5xl">{{ $t('Le fondateur','The founder') }}</h2>
<p class="mt-6 font-serif text-2xl text-ink">{{ $founder?->name ?? $t('Ir Roger Manema Cirhahingirwa','Ir Roger Manema Cirhahingirwa') }}</p>
@if ($founder)
<p class="mt-1 font-semibold text-moss">{{ $founder->getTranslation('role', $locale) }}</p>
@if ($bio = $founder->getTranslation('bio', $locale))<p class="mt-5 leading-relaxed text-ink/75">{{ $bio }}</p>@endif
@if ($quote = $founder->getTranslation('quote', $locale))<blockquote class="mt-8 border-l-4 border-ochre bg-white p-6 font-serif text-xl leading-snug">&laquo; {{ $quote }} &raquo;</blockquote>@endif
@else
<p class="mt-1 font-semibold text-moss">{{ $t('Fondateur et Representant national','Founder and National Representative') }}</p>
<p class="mt-5 leading-relaxed text-ink/75">{{ $t("Ingenieur, il fonde l'organisation le 10 mai 1995 et la represente aujourd'hui en qualite de Representant national. Il est le responsable de l'OAAT et l'interlocuteur de ses partenaires.","An engineer, he founded the organisation on 10 May 1995 and represents it today as National Representative. He heads OAAT and is the point of contact for its partners.") }}</p>
<blockquote class="mt-8 border-l-4 border-ochre bg-white p-6 font-serif text-xl leading-snug">&laquo; {{ $t("Ce n'est pas normal qu'il y ait toujours des gens pour demander et d'autres pour donner.","It is not normal that there are always people asking and others giving.") }} &raquo;</blockquote>
@endif
<a href="{{ route('contact', ['locale' => $locale]) }}" class="relative overflow-hidden before:absolute before:inset-y-0 before:-left-full before:w-1/2 before:-skew-x-12 before:bg-white/40 before:transition-all before:duration-700 hover:before:left-[150%] mt-8 inline-block bg-lake px-7 py-3.5 font-semibold text-white transition hover:bg-deep hover:shadow-lg">{{ $t('Contacter le fondateur','Contact the founder') }}</a>
</div></div></section>
{{-- RECONNAISSANCE --}}
<section id="reconnaissance" class="mx-auto max-w-7xl scroll-mt-32 px-5 py-24 lg:px-8">
<div data-r><h2 class="font-serif text-4xl font-semibold text-lake md:text-5xl">{{ $t("Reconnaissance officielle","Official recognition") }}</h2><p class="mt-4 max-w-2xl text-ink/70">{{ $t("Une organisation enregistree et reconnue par les autorites judiciaires, administratives et de planification.","An organisation registered and recognised by the judicial, administrative and planning authorities.") }}</p></div>
<div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
@forelse ($recognitions as $rec)
@php [$title, $authority, $desc] = $rec; @endphp
<div data-r class="sp group relative overflow-hidden border border-line bg-white p-6 transition duration-300 before:pointer-events-none before:absolute before:inset-0 before:opacity-0 before:transition before:duration-300 before:bg-[radial-gradient(240px_circle_at_var(--x,50%)_var(--y,50%),rgba(23,80,143,.09),transparent_70%)] hover:-translate-y-1 hover:border-lake hover:shadow-xl hover:before:opacity-100">
<span class="relative grid h-11 w-11 place-items-center bg-moss/10 text-moss"><svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6zM9 12l2 2 4-4"/></svg></span>
<h3 class="relative mt-4 font-serif text-lg font-semibold text-lake">{{ $title }}</h3>
<p class="relative mt-2 break-words font-mono text-xs text-ink/70">{{ $desc }}</p>
<p class="relative mt-3 text-sm text-ink/65">{{ $authority }}</p>
</div>
@empty
<p class="col-span-3 text-center text-sm text-ink/40 italic">{{ $t("Aucune reconnaissance pour le moment.","No recognitions yet.") }}</p>
@endforelse
</div></section>
{{-- ZONES --}}
<section id="zones" class="scroll-mt-32 bg-deep py-24 text-white">
<div class="mx-auto grid max-w-7xl gap-12 px-5 lg:grid-cols-5 lg:px-8">
<div class="lg:col-span-2" data-r>
<h2 class="font-serif text-4xl font-semibold leading-tight">{{ $t("La ou nous intervenons","Where we work") }}</h2>
<p class="mt-4 text-white/70">{{ $t("Treize zones reparties entre le Sud-Kivu et le Nord-Kivu. Selectionnez une province.","Thirteen areas spread across South Kivu and North Kivu. Select a province.") }}</p>
<div id="zt" class="mt-8 flex gap-2" role="tablist" aria-label="{{ $t('Provinces','Provinces') }}">
@foreach ($zones as $province => $entries)
@php
$provLabel = match ($province) {
    'Sud-Kivu'  => $t('Sud-Kivu', 'South Kivu'),
    'Nord-Kivu' => $t('Nord-Kivu', 'North Kivu'),
    default     => $province,
};
@endphp
<button type="button" data-zt="{{ $province }}" role="tab" aria-selected="{{ $loop->first ? 'true' : 'false' }}" class="rounded-full px-5 py-2.5 text-sm font-semibold transition {{ $loop->first ? 'bg-ochre text-deep' : 'border border-white/30 hover:bg-white/10' }}">{{ $provLabel }}</button>
@endforeach
</div></div>
@php $firstProvince = array_key_first($zones); @endphp
<div id="zp" class="grid gap-3 transition duration-300 sm:grid-cols-2 lg:col-span-3">
@forelse ($zones as $province => $entries)
@foreach ($entries as $zone)
<div data-zp="{{ $province }}" class="border border-white/15 bg-white/5 p-5 transition duration-300 hover:border-ochre hover:bg-white/10{{ $province === $firstProvince ? '' : ' hidden' }}">
<b class="font-serif text-lg">{{ $zone[0] }}</b><p class="mt-1 text-sm text-white/65">{{ $zone[1] }}</p>
</div>
@endforeach
@empty
<p class="col-span-2 text-center text-sm italic text-white/40">{{ $t('Aucune zone pour le moment.','No areas yet.') }}</p>
@endforelse
</div>
</div></section>
{{-- CTA --}}
<section class="bg-lake text-white">
<div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-8 px-5 py-16 lg:flex-row lg:items-center lg:px-8">
<div data-r>
<h2 class="font-serif text-3xl font-semibold md:text-4xl">{{ $t("Un projet, un partenariat ?","A project, a partnership?") }}</h2>
<p class="mt-3 max-w-xl text-white/80">{{ $t("Parlons de la facon dont l'OAAT peut agir avec vous.","Let's discuss how OAAT can act with you.") }}</p>
</div>
<div class="flex flex-wrap gap-3">
<a href="{{ route('contact', ['locale' => $locale]) }}" class="relative overflow-hidden before:absolute before:inset-y-0 before:-left-full before:w-1/2 before:-skew-x-12 before:bg-white/40 before:transition-all before:duration-700 hover:before:left-[150%] bg-ochre px-7 py-3.5 font-semibold text-deep transition hover:-translate-y-0.5 hover:shadow-xl">{{ $t("Nous contacter","Contact us") }}</a>
<a href="{{ route('projets.index', ['locale' => $locale]) }}" class="border border-white/40 px-7 py-3.5 font-semibold transition hover:bg-white hover:text-deep">{{ $t("Voir les projets","View projects") }}</a>
</div></div></section>
