@props(['eras', 'quote' => null, 'prefix' => 'new', 'useOld' => true, 'autofocus' => false])

@php
    $value = fn (string $key, $default = null) => $useOld ? old($key, $default) : $default;
@endphp

<div class="space-y-5">
    <div>
        <label for="{{ $prefix }}-content" class="label">البيت</label>
        <textarea id="{{ $prefix }}-content" name="content" dir="auto" rows="3" required minlength="3" maxlength="155"
            placeholder="اكتب البيت هنا…" @if ($autofocus) autofocus @endif
            class="input peer min-h-28 resize-none font-verse text-2xl leading-loose user-invalid:border-red-500">{{ $value('content', $quote?->content) }}</textarea>
        <p class="mt-1 text-xs text-gray-500 peer-user-invalid:text-red-600">من 3 إلى 155 حرفًا.</p>
    </div>

    <div>
        <label for="{{ $prefix }}-poet" class="label">الشاعر <span class="font-normal text-gray-500">(اختياري)</span></label>
        <input id="{{ $prefix }}-poet" name="poet" type="text" dir="auto" maxlength="60"
            value="{{ $value('poet', $quote?->poet) }}" placeholder="مثال: المتنبي" class="input">
    </div>

    <x-era-picker :eras="$eras" :selected="$value('era_id', $quote?->era_id)" :prefix="$prefix" />
</div>
