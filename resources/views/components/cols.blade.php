@props(['gutter' => $gutter ?? ($gap ?? '8')])

<div {{ $attributes->merge(['class' => 'flex flex-wrap ' . "-mx-$gutter"]) }}>
    {{ $slot }}
</div>
