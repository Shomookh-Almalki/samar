@props(['era'])

@php
    $colors = [
        'pre-islamic' => 'bg-amber-100 text-amber-900 ring-amber-300',
        'early-islamic' => 'bg-emerald-100 text-emerald-900 ring-emerald-300',
        'umayyad' => 'bg-rose-100 text-rose-900 ring-rose-300',
        'abbasid' => 'bg-cyan-100 text-cyan-900 ring-cyan-300',
        'andalusian' => 'bg-orange-100 text-orange-900 ring-orange-300',
        'mamluk-ottoman' => 'bg-violet-100 text-violet-900 ring-violet-300',
        'modern' => 'bg-sky-100 text-sky-900 ring-sky-300',
    ];
@endphp

<span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold ring-1 {{ $colors[$era->slug] ?? 'bg-stone-100 text-stone-800 ring-stone-300' }}">
    {{ $era->name_ar }}
</span>
