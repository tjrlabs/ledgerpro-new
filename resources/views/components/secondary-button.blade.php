<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn-secondary uppercase tracking-[0.14em] text-xs disabled:opacity-50']) }}>
    {{ $slot }}
</button>
