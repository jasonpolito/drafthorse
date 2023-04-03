@foreach ($attributes['data'] as $block)
@php
// dd($page->data['block_test']);
$component = 'blocks.' . $block['type'];
$data = $block['data'];
@endphp
{{-- @if (View::exists("components.$component")) --}}
<x-dynamic-component :component="$component" :data="$data" />
{{-- @endif --}}
@endforeach