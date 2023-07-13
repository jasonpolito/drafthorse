@props([
    'name', //
    'data',
])
@if (isset($name))
    @if (isset($data->$name))
        @foreach ($data->$name as $item)
            @php
                $data = App\Models\Record::getValueData($item->data);
                $block = App\Models\Block::firstWhere('uuid', $item->block);
                if (!$block) {
                    $block = App\Models\Partial::firstWhere('uuid', $item->block);
                }
            @endphp
            @if ($block)
                {!! App\Models\Record::renderMarkup($block->markup, ['data' => $data], true) !!}
            @endif
        @endforeach
    @endif
@endif
