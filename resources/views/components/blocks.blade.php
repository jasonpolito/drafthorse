@props(['name', 'data'])
{{-- {{ dd($data->$name) }} --}}
@if (isset($data->$name))
    @if ($data->$name->type == 'blocks')
        @foreach ($data->$name->value as $item)
            @php
                $data = App\Models\Record::getValueData($item->data);
                $template = App\Models\Template::find($item->template);
            @endphp
            {!! App\Models\Record::renderTemplate($template->markup, ['data' => $data], true) !!}
        @endforeach
    @endif
@endif
