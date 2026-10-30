@props(['quote', 'liked' => false, 'anchor' => null])

@php
    $heart = 'M12 20.5s-7.5-4.6-9.2-9.4C1.7 7.9 3.6 5 6.6 5c1.9 0 3.4 1 5.4 3.1C14 6 15.5 5 17.4 5c3 0 4.9 2.9 3.8 6.1C19.5 15.9 12 20.5 12 20.5z';
@endphp

@auth
    <form method="POST" action="{{ route('quotes.like', $quote) }}">
        @csrf
        @if ($anchor)
            <input type="hidden" name="anchor" value="{{ $anchor }}">
        @endif
        <button type="submit" aria-pressed="{{ $liked ? 'true' : 'false' }}"
            aria-label="{{ $liked ? 'إلغاء الإعجاب' : 'إعجاب' }}"
            class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-sm font-semibold transition {{ $liked ? 'bg-coral/15 text-coral' : 'text-ink/60 hover:bg-coral/10 hover:text-coral' }}">
            <svg viewBox="0 0 24 24" class="h-5 w-5" stroke="currentColor" stroke-width="2" fill="{{ $liked ? 'currentColor' : 'none' }}" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $heart }}" />
            </svg>
            <span>{{ $quote->liked_by_count }}</span>
        </button>
    </form>
@else
    {{-- Guests: pressing the heart takes them to the log in page --}}
    <a href="{{ route('login') }}" title="سجّل الدخول للإعجاب" aria-label="سجّل الدخول للإعجاب"
        class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-sm font-semibold text-ink/60 transition hover:bg-coral/10 hover:text-coral">
        <svg viewBox="0 0 24 24" class="h-5 w-5" stroke="currentColor" stroke-width="2" fill="none" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $heart }}" />
        </svg>
        <span>{{ $quote->liked_by_count }}</span>
    </a>
@endauth
