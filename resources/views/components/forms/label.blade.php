@props(['for', 'value', 'required' => false])

<label for="{{ $for }}" {{ $attributes->merge(['class' => 'block text-sm font-bold text-violet-100']) }}>
    {{ $value ?? $slot }}
    @if($required)
        <span class="text-red-500">*</span>
    @endif
</label>
