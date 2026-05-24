@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-lg border-violet-500/25 bg-black/45 text-violet-50 shadow-sm shadow-black/30 focus:border-violet-400 focus:ring-4 focus:ring-violet-500/20']) }}>
