@php
$data = $attributes['data'];
$img = Storage::url($data['bg_image'] ?? '');
@endphp
<x-section class="bg-blue-500" style="background-image: url({{ $img }})">
    <div class="absolute top-0 left-0 w-full h-full" style="background: {{ $data['bg_image_overlay'] ?? '' }}"></div>
    <x-container class="max-w-3xl">
        <x-prose class="prose-invert">
            {!! $data['content'] !!}
        </x-prose>
    </x-container>
</x-section>

{!! Str::replace('selector', '#afasd', $data['custom_code'] ?? '') !!}

<table>
    <tbody>
        <th></th>
    </tbody>
</table>