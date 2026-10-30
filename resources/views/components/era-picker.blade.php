@props(['eras', 'selected' => null, 'prefix' => 'era'])

<fieldset>
    <legend class="label">العصر <span class="font-normal text-gray-500">(إلزامي)</span></legend>

    <div class="flex flex-wrap gap-2">
        @foreach ($eras as $era)
            <label class="cursor-pointer">
                <input type="radio" name="era_id" value="{{ $era->id }}" id="{{ $prefix }}-era-{{ $era->id }}"
                    class="peer sr-only" required @checked((string) $selected === (string) $era->id)>
                <span
                    class="inline-flex items-center rounded-full bg-white px-4 py-1.5 text-sm font-semibold ring-1 ring-sand transition hover:bg-lagoon-soft peer-checked:bg-lagoon peer-checked:text-white peer-checked:ring-lagoon peer-focus-visible:ring-4 peer-focus-visible:ring-lagoon/30">
                    {{ $era->name_ar }}
                </span>
            </label>
        @endforeach
    </div>
</fieldset>
