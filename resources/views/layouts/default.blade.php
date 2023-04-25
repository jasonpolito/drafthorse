<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    {{-- @vite('resources/css/app.css') --}}
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        clifford: '#da373d',
                    }
                }
            }
        }
    </script>
    <style>
        * {
            position: relative;
        }
    </style>
</head>

<body>
    <div class="bg-primary-500"></div>
    @yield('content')
    <div class="fixed bottom-0 right-0 z-50 p-4"><a
           href="{{ route('filament.resources.records.edit', ['record' => $page]) }}"
           class="block p-3 text-white rounded-full bg-slate-900">
            <x-heroicon-o-pencil class="w-5 h-5" />
        </a>
    </div>
</body>

</html>
