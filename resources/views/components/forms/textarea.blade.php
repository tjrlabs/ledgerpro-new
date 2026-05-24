<div>
    <label for="{{$attributes['id']}}">{{$attributes['label']}}</label>
    <div class="app-field mt-1 flex justify-center items-end gap-x-3 pr-4">
        <textarea {{$attributes->merge(['class' => "h-[120px] text-violet-50 resize-none text-sm w-full p-4 bg-transparent rounded-none focus:ring-0 focus:ring-offset-0 focus-visible:outline-0 focus-visible:ring-0 focus-visible:ring-offset-0 !border-0"])}}>{{$slot}}</textarea>
        <span class="error-icon"></span>
        <span class="after-icon text-violet-200/50">
            {{$afterIcon ?? ''}}
        </span>
    </div>
    <p class="text-xs text-violet-200/55 my-1" id="fl_desc_{{$attributes['id']}}">{{$attributes['description']}}</p>
</div>
