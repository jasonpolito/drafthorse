<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    @if (request()->input('light'))
        <script>
            tailwind.config = {
                darkMode: 'class'
            }
        </script>
    @endif
    <title>Document</title>
</head>

<body class="py-16 dark:bg-slate-900">
    <div class="py-32 bg-slate-800">
        <x-container>
            <h1 class="mb-8 text-5xl text-white">&#123;&#123; $data->title &#125;&#125;</h1>
            <div class="max-w-2xl font-light text-slate-500">&#123;&#123; $data->content &#125;&#125;</div>
        </x-container>
    </div>
</body>

</html>
