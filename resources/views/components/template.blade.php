@props(['name', 'data'])

@php
    $template = App\Models\Template::where('name', 'like', "%$name%")->first();
@endphp
{!! App\Models\Record::renderTemplate($template->markup, ['data' => $data]) !!}
