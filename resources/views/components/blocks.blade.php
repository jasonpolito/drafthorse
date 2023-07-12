@props([
    'name', //
    'data',
    'isLayout' => false,
])
@if ($isLayout)
    @if (isset($data->$name))
        @foreach ($data->$name as $item)
            @php
                $data = App\Models\Record::getValueData($item->data);
                $block = App\Models\Block::find($item->block);
            @endphp
            @if ($block)
                {!! App\Models\Record::renderMarkup($block->markup, ['data' => $data], true) !!}
            @endif
        @endforeach
    @endif
@else
    @if (isset($name))
        @if (isset($data->$name))
            @foreach ($data->$name as $item)
                @php
                    $data = App\Models\Record::getValueData($item->data);
                    $block = App\Models\Block::find($item->block);
                @endphp
                @if ($block)
                    {!! App\Models\Record::renderMarkup($block->markup, ['data' => $data], true) !!}
                @endif
            @endforeach
        @endif
    @endif
@endif
