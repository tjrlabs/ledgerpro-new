@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-semibold tracking-tight text-violet-100']) }}>
    {{ $value ?? $slot }}
</label>
