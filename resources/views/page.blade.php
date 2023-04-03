@extends('layouts.default')
@section('content')
{{ dd($page->getData()) }}
@foreach ($page->getTaxonomyFields() as $name => $block)
@php
$component = 'blocks.' . $block['type'];
$data = $block['data'];
@endphp
@if (View::exists("components.$component"))
<x-dynamic-component :component="$component" :data="$data" :page="$page" />
@endif
@endforeach
@endsection