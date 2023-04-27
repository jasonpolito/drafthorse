<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PL Pricing Calc</title>
    {{-- @vite('resources/css/app.css') --}}
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        const primary = {
            DEFAULT: '#FB2250',
            50: '#FED6DF',
            100: '#FEC2CF',
            200: '#FD9AAF',
            300: '#FC728F',
            400: '#FC4A70',
            500: '#FB2250',
            600: '#E10433',
            700: '#AA0326',
            800: '#73021A',
            900: '#3C010D',
            950: '#200107'
        };
    </script>
    <script>
        const color = 'teal';
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: tailwind.colors[color][500],
                            ...tailwind.colors[color]
                        }
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        * {
            position: relative;
        }
    </style>
</head>

<body>
    @yield('content')
</body>

</html>
