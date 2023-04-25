@props(['name'])

@php
    $template = App\Models\Template::where('name', 'like', "%$name%")->first();
@endphp
<div>
    {!! Blade::render($template->markup) !!}
</div>
