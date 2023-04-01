@php
@endphp
<section {{ $attributes->merge(['class' => 'py-12 md:py-16 lg:py-24 bg-cover bg-center']) }}>
    {{ $slot }}
</section>