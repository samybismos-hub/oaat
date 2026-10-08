{{--
    _contact.blade.php — Section Contact (formulaire + choix).
    ────────────────────────────────────────────────────────────────
    Variables disponibles :
        $settings  (Setting|null) — paramètres du site
        $locale    (string)
        $t         (helper)
--}}
@php
    $collabTypes = [
        ['p', $t('Devenir partenaire', 'Become a partner'),
         $t("Co-construire un programme avec l'OAAT.", "Co-design a programme with OAAT."),
         'M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM22 21v-2a4 4 0 00-3-3.9M16 3.1a4 4 0 010 7.8'],
        ['f', $t('Financer un projet', 'Fund a project'),
         $t('Soutenir un projet prêt à être financé.', 'Support a project that is ready for funding.'),
         'M12 2v20M17 6H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6'],
        ['i', $t("Demande d'information", 'Information request'),
         $t('Rapports, documents et renseignements.', 'Reports, documents and information.'),
         'M12 16v-4M12 8h.01M12 22a10 10 0 100-20 10 10 0 000 20z'],
    ];
@endphp

<section id="contact"
         class="mx-auto max-w-7xl scroll-mt-20 px-5 py-24 lg:px-8">

    <div class="grid gap-12 lg:grid-cols-5">

        {{-- Colonne gauche : types de collaboration --}}
        <div class="lg:col-span-2" data-r>
            <h2 class="font-serif text-4xl font-semibold text-lake md:text-5xl">
                {{ $t('Contact', 'Contact') }}
            </h2>
            <p class="mt-4 text-ink/70">
                {{ $t("Choisissez le type de collaboration qui vous correspond, puis écrivez-nous.",
                      "Choose the kind of collaboration that suits you, then write to us.") }}
            </p>

            <div id="pw" class="mt-8 space-y-3">
                @foreach ($collabTypes as $w)
                    <button type="button" data-v="{{ $w[0] }}"
                            class="pw sp group relative flex w-full items-center gap-4 overflow-hidden border border-line bg-white p-5 text-left transition duration-300
                                   before:pointer-events-none before:absolute before:inset-0 before:opacity-0 before:transition before:duration-300
                                   before:bg-[radial-gradient(220px_circle_at_var(--x,50%)_var(--y,50%),rgba(23,80,143,.10),transparent_70%)]
                                   hover:-translate-y-0.5 hover:border-lake hover:shadow-lg hover:before:opacity-100">
                        <span class="relative grid h-12 w-12 shrink-0 place-items-center bg-lake/10 text-lake transition group-hover:bg-lake group-hover:text-white">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="{{ $w[3] }}"/>
                            </svg>
                        </span>
                        <span class="relative grow">
                            <b class="block text-lake">{{ $w[1] }}</b>
                            <span class="text-sm text-ink/65">{{ $w[2] }}</span>
                        </span>
                        <span class="relative text-lake transition group-hover:translate-x-1">→</span>
                    </button>
                @endforeach
            </div>

            <a href="#coordonnees"
               class="mt-6 inline-block font-semibold text-lake underline-offset-4 hover:underline">
                {{ $t('Coordonnées complètes ↓', 'Full contact details ↓') }}
            </a>
        </div>
{{-- Colonne droite : formulaire --}}
        <form id="cf" method="POST"
              action="{{ route('contact.send', ['locale' => $locale]) }}"
              novalidate
              class="grid gap-4 border border-line bg-white p-6 shadow-xl sm:grid-cols-2 md:p-8 lg:col-span-3">

            @csrf

            @if (session('success'))
                <div class="sm:col-span-2 rounded-lg bg-moss/15 px-4 py-3 text-sm font-semibold text-moss" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <label class="text-sm font-medium">
                {{ $t('Nom complet', 'Full name') }}
                <input name="name" type="text" autocomplete="name" value="{{ old('name') }}"
                       class="mt-1.5 w-full border border-line px-4 py-3 outline-none transition focus:border-lake focus:ring-4 focus:ring-lake/15" required>
                <p class="er mt-1 hidden text-xs text-red-600">{{ $t('Indiquez votre nom.', 'Enter your name.') }}</p>
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </label>

            <label class="text-sm font-medium">
                {{ $t('E-mail', 'Email') }}
                <input name="email" type="email" autocomplete="email" value="{{ old('email') }}"
                       class="mt-1.5 w-full border border-line px-4 py-3 outline-none transition focus:border-lake focus:ring-4 focus:ring-lake/15" required>
                <p class="er mt-1 hidden text-xs text-red-600">{{ $t('Saisissez une adresse e-mail valide.', 'Enter a valid email address.') }}</p>
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </label>

            <label class="text-sm font-medium sm:col-span-2">
                {{ $t('Objet', 'Subject') }}
                <select name="subject"
                        class="mt-1.5 w-full border border-line bg-white px-4 py-3 outline-none transition focus:border-lake focus:ring-4 focus:ring-lake/15">
                    <option value="p" {{ old('subject')==='p'?'selected':'' }}>{{ $t('Proposer un partenariat', 'Propose a partnership') }}</option>
                    <option value="f" {{ old('subject')==='f'?'selected':'' }}>{{ $t('Financer un projet', 'Fund a project') }}</option>
                    <option value="i" {{ old('subject')==='i'?'selected':'' }}>{{ $t("Demande d'information", 'Information request') }}</option>
                    <option value="m" {{ old('subject')==='m'?'selected':'' }}>{{ $t('Presse', 'Press') }}</option>
                </select>
            </label>

            <label class="text-sm font-medium sm:col-span-2">
                {{ $t('Message', 'Message') }}
                <textarea name="message" rows="5" required
                          class="mt-1.5 w-full border border-line px-4 py-3 outline-none transition focus:border-lake focus:ring-4 focus:ring-lake/15">{{ old('message') }}</textarea>
                <p class="er mt-1 hidden text-xs text-red-600">{{ $t('Votre message doit contenir au moins 10 caractères.', 'Your message must be at least 10 characters.') }}</p>
                @error('message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </label>

            <button id="sb" type="submit"
                    class="rounded-full bg-lake px-7 py-3.5 font-semibold text-white transition hover:bg-deep hover:shadow-lg active:scale-95 disabled:opacity-60 sm:col-span-2 sm:justify-self-start
                           relative overflow-hidden before:absolute before:inset-y-0 before:-left-full before:w-1/2 before:-skew-x-12 before:bg-white/40 before:transition-all before:duration-700 hover:before:left-[150%]">
                {{ $t('Envoyer le message', 'Send message') }}
            </button>

        </form>

    </div>
</section>