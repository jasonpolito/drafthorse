@props(['name', 'data'])

@php
    $block = App\Models\Block::where('name', 'like', "%$name%")->first();
    $data = json_decode(json_encode($data));
@endphp
{!! App\Models\Record::renderMarkup($block->markup, ['data' => $data]) !!}
