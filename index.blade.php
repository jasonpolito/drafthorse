<div class="py-16 bg-center bg-cover" style="background-image: url({{ Storage::url($data->background ?? '') }})">
    <div class="absolute top-0 left-0 w-full h-full" style="background: {{ $data->color }}"></div>
    <div class="container px-16 mx-auto">
        <h1 class="text-7xl">{{ $data->title }}</h1>
    </div>
</div>

<ul class="flex gap-6">
    @foreach ($data->cols as $col)
        <li class="w-1/4">
            <div class="py-20 mb-6 bg-center bg-cover"
                 style="background-image: url({{ Storage::url($col->image ?? '') }})"></div>
            {{ $col->title }}
        </li>
    @endforeach
</ul>
