@extends('layouts.default')
@section('content')
@foreach ((array) $page->getBlocks() as $block)
@php
$component = 'blocks.' . $block['type'];
$data = $block['data'];
@endphp
<x-dynamic-component :component="$component" :data="$data" />
@endforeach
@endsection