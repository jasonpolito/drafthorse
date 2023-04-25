@extends('layouts.test')
@section('content')
    {!! App\Models\Record::renderTemplate($record->data['template'], ['data' => $record->getData()]) !!}
@endsection
