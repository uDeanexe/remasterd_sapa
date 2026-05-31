@php
    $label = config('app.name', 'Sapa');
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-xl bg-slate-900 px-3 py-2 text-sm font-black tracking-wide text-white']) }}>
    {{ strtoupper(mb_substr($label, 0, 2)) }}
</span>
