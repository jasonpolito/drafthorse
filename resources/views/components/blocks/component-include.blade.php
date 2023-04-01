@php
$id = $attributes['data']['component_id'];
$component = App\Models\Component::find($id);
@endphp
<div>
    @foreach ($component->blocks as $block)
    @php
    $component = 'blocks.' . $block['type'];
    $data = new \Illuminate\View\ComponentAttributeBag($block['data']);
    @endphp
    <x-dynamic-component :component="$component" :data="$data" />

    @endforeach
</div>