@php ob_start(); @endphp
@php
    $layout = explode('@content', App\Models\Layout::find($record->data['layout'])->markup);
    $start = Str::replace('<x-block ', '<x-block :$data ', $layout[0]);
    $end = $layout[1];
@endphp
{!! $start !!}
{!! App\Models\Record::renderMarkup($record->data['markup'], ['data' => App\Models\Record::getData($record)]) !!}
{!! $end !!}
@php
    $markup = ob_get_contents();
ob_end_clean(); @endphp
{!! Blade::render($markup, [
    'record' => $record,
    'data' => json_decode(json_encode(App\Models\Record::getData($record))),
]) !!}
