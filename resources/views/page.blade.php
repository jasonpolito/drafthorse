@extends('layouts.default')
@section('content')
    {!! Blade::render($page->data['template'], $page->getData()) !!}
@endsection
