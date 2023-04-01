<x-section :bg-color="$attributes['data']['bg-color']">
    <x-container class="max-w-3xl">
        <x-prose>{!! $attributes['data']['content'] !!}</x-prose>
    </x-container>
</x-section>