@props(['name', 'data'])

@php
    $template = App\Models\Template::where('name', 'like', "%$name%")->first();
    $data = json_decode(json_encode($data));
@endphp
{!! App\Models\Record::renderTemplate($template->markup, ['data' => $data]) !!}

@foreach ($data as $item)
    @if (isset($item->type))
        @if ($item->type == 'blocks')
            @foreach ($item->value as $elem)
                @php
                    $data = App\Models\Record::getValueData($elem->data);
                    $template = App\Models\Template::find($elem->template);
                @endphp
                {!! App\Models\Record::renderTemplate($template->markup, ['data' => $data], true) !!}
            @endforeach
        @endif
    @endif
@endforeach
