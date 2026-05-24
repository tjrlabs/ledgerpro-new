<div>
    <label for="{{ $attributes['id'] }}" class="flex items-center cursor-pointer select-none">
        <div class="relative">
            <input type="checkbox" {{$attributes->merge(['class' => "sr-only peer"])}} />
            <div class="w-11 h-6 bg-violet-950 rounded-full peer-checked:bg-violet-500 transition-colors duration-300"></div>
            <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-violet-50 rounded-full shadow-md transform peer-checked:translate-x-5 transition-transform duration-300"></div>
        </div>
        <span class="ml-3 text-violet-100">@if($attributes['icon'] && $attributes['icon'] !== '') <i class="fa {{$attributes['icon']}}"></i> @endif {{ $attributes['label'] }} </span>
    </label>
    <p class="text-xs text-violet-200/55 my-1" id="fl_desc_{{$attributes['id']}}">{{$attributes['description']}}</p>
</div>
