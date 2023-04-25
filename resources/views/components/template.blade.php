@props(['name', 'data'])

@php
    $template = App\Models\Template::where('name', 'like', "%$name%")->first();
@endphp
<div>
    {!! Blade::render(Str::replace('<x-template', '<x-template :$data ', $template->markup), ['data' => $data]) !!}
</div>
