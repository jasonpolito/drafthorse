@php
    // dd($record->getData());
@endphp
@php ob_start(); @endphp
@php
    $layout = explode('@content', App\Models\Layout::find($record->data['layout'])->markup);
    $start = Str::replace('<x-template ', '<x-template :$data ', $layout[0]);
    $end = $layout[1];
@endphp
{!! $start !!}
{!! App\Models\Record::renderTemplate($record->data['template'], ['data' => $record->getData()]) !!}
{!! $end !!}
@php
    $markup = ob_get_contents();
ob_end_clean(); @endphp
{!! Blade::render($markup, [
    'record' => $record,
    'data' => json_decode(json_encode($record->getData())),
]) !!}
