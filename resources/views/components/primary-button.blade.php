<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-primary uppercase tracking-[0.14em] text-xs']) }}>
    {{ $slot }}
</button>
