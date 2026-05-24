<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-danger uppercase tracking-[0.18em] text-xs']) }}>
    {{ $slot }}
</button>
