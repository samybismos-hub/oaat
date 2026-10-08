{{--
    _besoin.blade.php — Section Besoin (formulaire de soumission).
    ──────────────────────────────────────────────────────────────────────────
    Variables disponibles :
        $locale    (string)
        $t         (helper)
--}}
<section id="besoin"
         class="mx-auto max-w-5xl scroll-mt-20 px-5 py-24 lg:px-8">

    <div class="grid gap-10">

        <div class="text-center" data-r>
            <h2 class="font-serif text-4xl font-semibold text-lake md:text-5xl">
                {{ $t('Soumettre un besoin', 'Submit a need') }}
            </h2>
            <p class="mt-4 text-ink/70 max-w-2xl mx-auto">
                {{ $t("Décrivez votre besoin et nous vous contacterons dans les plus brefs délais.",
                      "Describe your need and we will get back to you shortly.") }}
            </p>
        </div>

        <form id="bf" method="POST"
              action="{{ route('besoin.send', ['locale' => $locale]) }}"
              novalidate
              class="grid gap-4 border border-line bg-white p-6 shadow-xl md:p-8">

            @csrf

            @if (session('success'))
                <div class="rounded-lg bg-moss/15 px-4 py-3 text-sm font-semibold text-moss" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="text-sm font-medium">
                    {{ $t('Nom complet', 'Full name') }}
                    <input name="contact_name" type="text" autocomplete="name" value="{{ old('contact_name') }}"
                           class="mt-1.5 w-full border border-line px-4 py-3 outline-none transition focus:border-lake focus:ring-4 focus:ring-lake/15" required>
                    @error('contact_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </label>

                <label class="text-sm font-medium">
                    {{ $t('E-mail', 'Email') }}
                    <input name="email" type="email" autocomplete="email" value="{{ old('email') }}"
                           class="mt-1.5 w-full border border-line px-4 py-3 outline-none transition focus:border-lake focus:ring-4 focus:ring-lake/15" required>
                    @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </label>
            </div>

            <label class="text-sm font-medium">
                {{ $t('Organisation (optionnel)', 'Organization (optional)') }}
                <input name="organization" type="text" autocomplete="organization" value="{{ old('organization') }}"
                       class="mt-1.5 w-full border border-line px-4 py-3 outline-none transition focus:border-lake focus:ring-4 focus:ring-lake/15">
                @error('organization')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </label>

            <label class="text-sm font-medium">
                {{ $t('Description du besoin', 'Need description') }}
                <textarea name="description" rows="6" required
                          class="mt-1.5 w-full border border-line px-4 py-3 outline-none transition focus:border-lake focus:ring-4 focus:ring-lake/15">{{ old('description') }}</textarea>
                @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </label>

            <button type="submit"
                    class="rounded-full bg-lake px-7 py-3.5 font-semibold text-white transition hover:bg-deep hover:shadow-lg active:scale-95 disabled:opacity-60 sm:justify-self-start
                           relative overflow-hidden before:absolute before:inset-y-0 before:-left-full before:w-1/2 before:-skew-x-12 before:bg-white/40 before:transition-all before:duration-700 hover:before:left-[150%]">
                {{ $t('Soumettre', 'Submit') }}
            </button>

        </form>

    </div>
</section>