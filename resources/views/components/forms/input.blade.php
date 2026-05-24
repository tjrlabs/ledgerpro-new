<div>
    <label for="{{$attributes['id']}}">{{$attributes['label']}}</label>

    <div class="app-field mt-1 flex items-center gap-x-3 pr-4">
        <input {{$attributes->merge(['class' => "h-[48px] text-sm text-violet-50 bg-transparent rounded-none w-full !border-0 focus-visible:ring-0 focus-visible:ring-offset-0 px-4 focus-visible:outline-hidden"])}}/>
        <span class="error-icon"></span>
        <span class="after-icon text-violet-200/50">
            {{$afterIcon}}
        </span>
    </div>
    <p class="text-xs text-violet-200/55 my-1" id="fl_desc_{{$attributes['id']}}">{{$attributes['description']}}</p>
</div>
