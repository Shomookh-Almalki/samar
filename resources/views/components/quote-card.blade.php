@props(['quote', 'liked' => false])

@php
    $isMine = auth()->check() && auth()->id() === $quote->user_id;
@endphp

<article id="quote-{{ $quote->id }}"
    class="relative overflow-hidden rounded-3xl bg-white p-6 shadow-lg shadow-lagoon/10 ring-1 transition hover:-translate-y-1 hover:shadow-xl {{ $isMine ? 'ring-2 ring-lagoon/40' : 'ring-sand' }}">
    <span class="pointer-events-none absolute -top-3 start-5 select-none font-verse text-8xl leading-none text-gold/30" aria-hidden="true">“</span>

    <div class="relative flex justify-end">
        <x-era-badge :era="$quote->era" />
    </div>

    <blockquote class="relative mt-3 text-center">
        <p dir="auto" class="font-verse text-balance text-2xl leading-[2.3] text-lagoon-deep md:text-3xl">{{ $quote->content }}</p>
        <footer class="mt-2 font-semibold text-gold-deep">— <span dir="auto">{{ $quote->poet ?: 'شاعر مجهول' }}</span></footer>
    </blockquote>

    <div class="mt-5 flex items-center justify-between gap-2 border-t border-sand pt-3 text-sm">
        <p class="min-w-0 truncate text-ink/60">
            <span class="font-semibold text-ink/80">{{ $quote->user->name }}</span>
            <span class="text-xs">· {{ $quote->created_at->format('Y-m-d') }}</span>
        </p>

        <div class="flex items-center gap-1">
            <x-like-button :quote="$quote" :liked="$liked" />

            @if ($isMine)
                <a href="{{ route('home', array_filter(['era' => request('era'), 'edit' => $quote->id])) }}#quote-{{ $quote->id }}"
                    aria-label="تعديل"
                    class="grid h-9 w-9 place-items-center rounded-full text-ink/60 transition hover:bg-lagoon-soft hover:text-lagoon-deep">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.9 3.6a2.1 2.1 0 0 1 3 3L7.5 19 3 20l1-4.5L16.9 3.6z" />
                    </svg>
                </a>

                <form method="POST" action="{{ route('quotes.destroy', $quote) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" aria-label="حذف"
                        class="grid h-9 w-9 place-items-center rounded-full text-ink/60 transition hover:bg-coral/10 hover:text-coral">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M10 11v6m4-6v6M6 7l1 12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-12M9 7V4h6v3" />
                        </svg>
                    </button>
                </form>
            @endif
        </div>
    </div>
</article>
