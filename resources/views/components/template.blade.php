@props(['name', 'data'])

@php
    $template = App\Models\Template::where('name', 'like', "%$name%")->first();
    $data = json_decode(json_encode($data));
@endphp
{!! App\Models\Record::renderTemplate($template->markup, ['data' => $data]) !!}
