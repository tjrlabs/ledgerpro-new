<div>
    <label for="{{$attributes['id']}}" class="font-semibold">{{$attributes['label']}}</label>

    <div class="flex justify-center items-center gap-x-3 mt-1 rounded-md bg-white border border-background pr-4 focus-within:border-primary">
        <input {{$attributes->merge(['class' => "h-[45px] text-sm text-neutral bg-transparent rounded-none w-full !border-0 focus-visible:ring-0 focus-visible:ring-offset-0 px-4 focus-visible:outline-none"])}}/>
        <span class="error-icon"></span>
        <span class="after-icon">
            {{$afterIcon}}
        </span>
    </div>
    <p class="text-xs text-gray my-1" id="fl_desc_{{$attributes['id']}}">{{$attributes['description']}}</p>
</div>
