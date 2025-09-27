<div>
    <label for="{{$attributes['id']}}" class="font-semibold">{{$attributes['label']}}</label>
    <div class="flex justify-center items-end gap-x-3 mt-1 rounded-md bg-white border border-background pr-4 focus-within:border-primary">
        <textarea {{$attributes->merge(['class' => "h-[100px] text-neutral resize-none text-sm w-full p-4 bg-transparent rounded-none focus:ring-0 focus:ring-offset-0 focus-visible:outline-0 focus-visible:ring-0 focus-visible:ring-offset-0 !border-0"])}}>{{$slot}}</textarea>
        <span class="error-icon"></span>
        <span class="after-icon">
            {{$afterIcon ?? ''}}
        </span>
    </div>
    <p class="text-xs text-gray my-1" id="fl_desc_{{$attributes['id']}}">{{$attributes['description']}}</p>
</div>
