@props(['name', 'data'])
{{-- {{ dd($name) }} --}}
@if (isset($data->$name))
    @if ($data->$name->type == 'blocks')
        @foreach ($data->$name->value as $item)
            @php
                $data = App\Models\Record::getValueData($item->data);
                $block = App\Models\Block::find($item->block);
            @endphp
            {!! App\Models\Record::renderMarkup($block->markup, ['data' => $data], true) !!}
        @endforeach
    @endif
@endif
