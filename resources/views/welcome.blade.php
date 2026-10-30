@extends('Layout.app')

@section('title', 'سمر · أبيات شعرية')
@section('content')
    @php
        $editingId = request()->integer('edit');
    @endphp

    {{-- Hero --}}
    <section class="hero-bg relative overflow-hidden text-white">
        <div class="pattern-stars absolute inset-0" aria-hidden="true"></div>

        <div class="relative mx-auto max-w-6xl px-4 pb-28 pt-14 text-center">
            <p class="font-brand text-6xl font-bold text-gold drop-shadow md:text-7xl">سمر</p>
            <h1 class="mt-4 font-verse text-4xl leading-snug md:text-6xl">بين بيت وبيت يحلو السمر</h1>
            <p class="mx-auto mt-5 max-w-2xl text-lg text-white/90 md:text-xl">
                شارك بيتًا من الشعر العربي، وضعه في عصره، واكتشف الأبيات التي يحبها الآخرون.
            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                @auth
                    <button type="button" popovertarget="quote-popup" class="btn-gold text-lg">شارك بيتًا</button>
                @else
                    <a href="{{ route('signup') }}" class="btn-gold text-lg">انضم إلى سمر</a>
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center rounded-full px-6 py-2.5 text-lg font-semibold text-white ring-1 ring-white/60 transition hover:bg-white/15">تسجيل الدخول</a>
                @endauth
            </div>
        </div>
    </section>

    <div class="relative mx-auto max-w-6xl px-4 pb-24 {{ $quoteOfTheDay ? '-mt-16' : 'pt-10' }}">

        @if ($errors->any())
            <div class="mb-6 rounded-2xl bg-coral/10 p-4 text-sm text-red-800 ring-1 ring-coral/40" role="alert">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Quote of the day: the most liked quote --}}
        @if ($quoteOfTheDay)
            <section class="mb-14" aria-label="بيت اليوم">
                <blockquote id="quote-of-the-day"
                    class="relative overflow-hidden rounded-[2rem] bg-white p-8 text-center shadow-xl shadow-lagoon/20 ring-2 ring-gold/60 md:p-12">
                    <div class="absolute inset-x-0 top-0 h-1.5 bg-linear-to-r from-lagoon via-gold to-coral"></div>

                    <h2 class="font-brand text-xl text-gold-deep">✦ بيت اليوم ✦</h2>

                    <p dir="auto" class="mx-auto mt-5 max-w-3xl font-verse text-balance text-3xl leading-[2.3] text-lagoon-deep md:text-4xl md:leading-[2.3]">{{ $quoteOfTheDay->content }}</p>
                    <p class="mt-3 text-xl font-semibold text-gold-deep">— <span dir="auto">{{ $quoteOfTheDay->poet ?: 'شاعر مجهول' }}</span></p>

                    <div class="mt-5 flex flex-wrap items-center justify-center gap-3 text-sm text-ink/70">
                        <x-era-badge :era="$quoteOfTheDay->era" />
                        <span>شاركه <span class="font-semibold">{{ $quoteOfTheDay->user->name }}</span> · {{ $quoteOfTheDay->created_at->format('Y-m-d') }}</span>
                        <x-like-button :quote="$quoteOfTheDay" :liked="$likedQuoteIds->contains($quoteOfTheDay->id)" anchor="quote-of-the-day" />
                    </div>
                </blockquote>
            </section>
        @endif

        {{-- Sections: one per era --}}
        <section aria-label="تصفح حسب العصر">
            <h2 class="mb-3 text-2xl font-bold text-lagoon-deep">تصفح حسب العصر</h2>

            <nav class="-mx-4 overflow-x-auto px-4 pb-3 md:overflow-visible" aria-label="العصور">
                <ul class="flex w-max gap-2 md:w-auto md:flex-wrap">
                    <li>
                        <a href="{{ route('home') }}" @if (! $currentEra) aria-current="page" @endif
                            class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition {{ $currentEra ? 'bg-white text-lagoon-deep ring-1 ring-sand hover:bg-lagoon-soft' : 'bg-lagoon-deep text-white shadow-md' }}">
                            الكل
                            <span class="rounded-full bg-black/10 px-2 text-xs">{{ $totalQuotes }}</span>
                        </a>
                    </li>
                    @foreach ($eras as $era)
                        @php $active = $currentEra?->is($era); @endphp
                        <li>
                            <a href="{{ route('home', ['era' => $era->slug]) }}" @if ($active) aria-current="page" @endif
                                class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition {{ $active ? 'bg-lagoon-deep text-white shadow-md' : 'bg-white text-lagoon-deep ring-1 ring-sand hover:bg-lagoon-soft' }}">
                                {{ $era->name_ar }}
                                <span class="rounded-full bg-black/10 px-2 text-xs">{{ $era->quotes_count }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </section>

        {{-- Quotes --}}
        <section class="mt-8" aria-label="الأبيات">
            <h2 class="mb-5 text-2xl font-bold text-lagoon-deep">
                {{ $currentEra ? $currentEra->name_ar : 'كل الأبيات' }}
                <span class="ms-2 text-base font-normal text-ink/60">({{ $quotes->count() }})</span>
            </h2>

            <div class="grid gap-6 md:grid-cols-2">
                @forelse ($quotes as $quote)
                    @if (auth()->check() && $quote->user_id === auth()->id() && $quote->id === $editingId)
                        {{-- Edit mode (my quote only) --}}
                        <form id="quote-{{ $quote->id }}" method="POST" action="{{ route('quotes.update', $quote) }}"
                            class="rounded-3xl bg-white p-6 shadow-lg shadow-lagoon/10 ring-2 ring-lagoon/40 md:col-span-2">
                            @csrf
                            @method('PUT')

                            <x-quote-fields :eras="$eras" :quote="$quote" prefix="edit" autofocus />

                            <div class="mt-6 flex gap-3">
                                <button type="submit" class="btn-primary">حفظ</button>
                                <a href="{{ route('home', array_filter(['era' => request('era')])) }}" class="btn-ghost">إلغاء</a>
                            </div>
                        </form>
                    @else
                        <x-quote-card :quote="$quote" :liked="$likedQuoteIds->contains($quote->id)" />
                    @endif
                @empty
                    <div class="rounded-3xl bg-white/70 p-10 text-center ring-1 ring-sand md:col-span-2">
                        <p class="font-verse text-4xl text-lagoon-deep">لا أبيات هنا بعد</p>
                        <p class="mt-2 text-ink/70">
                            {{ $currentEra ? 'لا توجد أبيات من هذا العصر بعد. شاركنا أول بيت!' : 'لا توجد أبيات بعد. شاركنا أول بيت!' }}
                        </p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    @auth
        {{-- Floating "+" button --}}
        <button type="button" popovertarget="quote-popup" aria-label="شارك بيتًا"
            class="fixed bottom-8 end-8 z-40 flex h-16 w-16 items-center justify-center rounded-full bg-linear-to-br from-coral to-gold text-white shadow-xl shadow-coral/40 transition hover:scale-110 focus:outline-none focus-visible:ring-4 focus-visible:ring-coral/40">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-8 w-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
        </button>

        {{-- Pop-up form --}}
        <div id="quote-popup" popover
            class="m-auto max-h-[92vh] w-[92%] max-w-xl overflow-y-auto rounded-3xl bg-cream p-0 shadow-2xl backdrop:bg-lagoon-deep/50 backdrop:backdrop-blur-sm">

            <form method="POST" action="{{ route('quotes.store') }}" class="p-6 md:p-8">
                @csrf
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="font-brand text-2xl font-bold text-lagoon-deep">شارك بيتًا</h2>
                    <button type="button" popovertarget="quote-popup" popovertargetaction="hide" aria-label="إغلاق"
                        class="rounded-full p-1 text-gray-400 hover:bg-white hover:text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <x-quote-fields :eras="$eras" prefix="new" :use-old="! $editingId" autofocus />

                <button type="submit" class="btn-primary mt-6 w-full text-lg">انشر</button>
            </form>
        </div>

        @if ($errors->any() && ! $editingId)
            {{-- Re-open the pop-up when sharing failed, so the user can fix the form --}}
            <script>
                document.getElementById('quote-popup')?.showPopover();
            </script>
        @endif
    @else
        <a href="{{ route('login') }}"
            class="fixed bottom-8 end-8 z-40 rounded-full bg-linear-to-br from-lagoon to-lagoon-deep px-6 py-4 font-semibold text-white shadow-xl shadow-lagoon/40 transition hover:-translate-y-0.5">
            سجّل الدخول لمشاركة بيت
        </a>
    @endauth
@endsection
