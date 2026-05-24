@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-violet-400 text-start text-base font-medium text-white bg-violet-500/15 focus:outline-hidden focus:text-white focus:bg-violet-500/20 focus:border-violet-300 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-violet-100/70 hover:text-white hover:bg-violet-500/10 hover:border-violet-400/40 focus:outline-hidden focus:text-white focus:bg-violet-500/10 focus:border-violet-400/40 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
