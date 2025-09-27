<div>
    <label for="{{$attributes['id']}}" class="font-semibold">{{$attributes['label']}}</label>
    <div class="flex justify-center items-center gap-x-3 mt-1 rounded-md bg-white border border-background pr-4 focus-within:border-primary">
        <div class="relative w-full">
            <select {{$attributes->merge(['class' => "h-[45px] text-neutral text-sm bg-transparent border-0 rounded-none w-full focus-visible:ring-0 focus-visible:ring-offset-0 px-4 focus-visible:outline-none appearance-none webkit-appearance-none !bg-none"])}}>
                {{$slot}}
            </select>
            <i class="fa fa-angle-down absolute right-[20px] dropdown-selector top-1/2 -translate-y-1/2 pointer-events-none"></i>
        </div>
        <span class="error-icon"></span>
        <span class="after-icon">
            {{$afterIcon}}
        </span>
    </div>
    <p class="text-xs text-gray mt-1" id="fl_desc_{{$attributes['id']}}">{{$attributes['description']}}</p>
</div>
