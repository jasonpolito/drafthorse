<nav class="py-4 text-white bg-slate-800">
    <x-container>
        <div class="flex items-center justify-between">
            <div>
                // Logo
            </div>
            <div>
                <ul class="flex">
                    @foreach ($data->links as $link)
                        <li>
                            <a href="{{ $link->url }}"
                               class="block px-4 py-2 rounded-md hover:bg-slate-900">{{ $link->text }}</a>
                        </li>
                    @endforeach
                    <li>
                        <a href="#" class="block px-4 py-2 rounded-md hover:bg-slate-900">
                            <x-heroicon-o-menu />
                        </a>

                    </li>
                </ul>
            </div>
        </div>
    </x-container>
</nav>
