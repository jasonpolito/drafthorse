@extends('layouts.default')
@section('content')
@foreach ($page->getBlocks() as $block)
@php
$component = 'blocks.' . $block['type'];
$data = $block['data'];
@endphp
@if (View::exists("components.$component"))
<x-dynamic-component :component="$component" :data="$data" />
@endif
@endforeach
@endsection