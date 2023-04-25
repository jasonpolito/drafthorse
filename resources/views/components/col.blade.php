@props(['gutter' => $gutter ?? ($gap ?? '8')])

<div {{ $attributes->merge(['class' => 'w-full ' . "px-$gutter"]) }}>
    {{ $slot }}
</div>
