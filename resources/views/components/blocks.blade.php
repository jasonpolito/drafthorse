@props(['name', 'data'])
@if (isset($data->$name))
    @if ($data->$name->type == 'blocks')
        @foreach ($data->$name->value as $item)
            @php
                $data = App\Models\Record::getValueData($item->data);
                $block = App\Models\Block::find($item->block);
                // dd($block);
            @endphp
            @if ($block)
                {!! App\Models\Record::renderMarkup($block->markup, ['data' => $data], true) !!}
            @endif
        @endforeach
    @endif
@endif
