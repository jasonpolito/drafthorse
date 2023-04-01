@php
$globals = App\Models\Component::global()->get();
$headers = $globals->filter(function($item) {
return $item->position == 'header';
});
@endphp


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @vite('resources/css/app.css')
</head>

<body>
    @foreach ($headers as $component)
    @foreach ($component->blocks as $block)
    @php
    $component = 'blocks.' . $block['type'];
    $data = new \Illuminate\View\ComponentAttributeBag($block['data']);
    @endphp
    <x-dynamic-component :component="$component" :attributes="$data" />

    @endforeach
    @endforeach
    @yield('content')
    <div class="fixed bottom-0 right-0 z-50 p-4"><a
            href="{{ route('filament.resources.pages.edit', ['record' => $page]) }}"
            class="block p-3 text-white rounded rounded-full bg-slate-900">
            <x-heroicon-o-pencil class="w-5 h-5" />
        </a>
    </div>
</body>

</html>