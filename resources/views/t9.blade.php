@php
    $intro = [
        'Technical Challenge' => 'Produce 3D printed food materials that are sustainable, safe, enjoyable, personalized, and meet supply chain needs',
        'Setting' => 'Food Deserts',
        'Intended Users' => 'Chronically food insecure populations residing in food deserts',
        'Policy Focus' => 'Supply chain policy & operations that support sustainable food systems; Product standards; Food safety regulations',
    ];
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    {{-- @vite('resources/css/app.css') --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="py-18 bg-slate-50 text-slate-800">
    <div class="flex">
        <div class="w-1/3 min-h-screen px-24 py-16">
            <ul>
                @foreach ($intro as $item => $value)
                    <li class="mb-8">
                        <div class="mb-2 text-2xl font-bold">{{ $item }}</div>
                        <div>{{ $value }}</div>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="w-2/3 min-h-screen px-24 py-16 bg-sky-800">
            <ul>
                @foreach ($intro as $item => $value)
                    <li class="mb-8">
                        <div class="mb-2 text-2xl font-bold">{{ $item }}</div>
                        <div>{{ $value }}</div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</body>

</html>
