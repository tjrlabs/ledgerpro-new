<div>
    <label for="{{$attributes['id']}}">{{$attributes['label']}}</label>
    <div class="app-field mt-1 flex justify-center items-center gap-x-3 pr-4">
        <div class="relative w-full">
            <select {{$attributes->merge(['class' => "h-[48px] text-violet-50 text-sm bg-transparent border-0 rounded-none w-full focus-visible:ring-0 focus-visible:ring-offset-0 px-4 focus-visible:outline-hidden appearance-none webkit-appearance-none !bg-none"])}}>
                {{$slot}}
            </select>
            <i class="fa fa-angle-down absolute right-[20px] dropdown-selector top-1/2 -translate-y-1/2 pointer-events-none text-violet-200/50"></i>
        </div>
        <span class="error-icon"></span>
        <span class="after-icon text-violet-200/50">
            {{$afterIcon}}
        </span>
    </div>
    <p class="text-xs text-violet-200/55 mt-1" id="fl_desc_{{$attributes['id']}}">{{$attributes['description']}}</p>
</div>
