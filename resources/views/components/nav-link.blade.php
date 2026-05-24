@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-violet-400 text-sm font-medium leading-5 text-white focus:outline-hidden focus:border-violet-300 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-violet-100/65 hover:text-white hover:border-violet-400/50 focus:outline-hidden focus:text-white focus:border-violet-400/50 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
