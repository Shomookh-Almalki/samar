@props(['title', 'subtitle'])

<div class="mx-auto grid max-w-6xl items-center gap-8 px-4 py-10 md:min-h-[calc(100vh-9rem)] md:grid-cols-2">
    <aside class="hero-bg relative hidden overflow-hidden rounded-[2rem] p-10 text-white shadow-xl shadow-lagoon/30 md:block">
        <div class="pattern-stars absolute inset-0" aria-hidden="true"></div>

        <div class="relative flex min-h-[28rem] flex-col justify-between">
            <p class="font-brand text-4xl">سمر <span class="text-base tracking-[0.3em] text-gold" dir="ltr">SAMAR</span></p>

            <figure class="my-10 text-center">
                <blockquote dir="rtl" lang="ar" class="font-verse text-3xl leading-[2.4]">
                    الخيل والليل والبيداء تعرفني<br>والسيف والرمح والقرطاس والقلم
                </blockquote>
                <figcaption class="mt-3 text-lg text-gold">— المتنبي</figcaption>
            </figure>

            <p class="text-sm text-white/85">شارك الأبيات التي تحبها، وضعها في عصرها، ودع الآخرين يقعون في حبها.</p>
        </div>
    </aside>

    <section class="rounded-[2rem] bg-white/80 p-8 shadow-xl shadow-lagoon/10 ring-1 ring-sand backdrop-blur md:p-10">
        <h1 class="font-brand text-3xl font-bold text-lagoon-deep">{{ $title }}</h1>
        <p class="mt-1 text-ink/70">{{ $subtitle }}</p>

        <div class="mt-6">
            {{ $slot }}
        </div>
    </section>
</div>
