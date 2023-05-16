<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data->meta_title ?? 'Page Title' }}</title>
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
    <style type="text/tailwindcss">
        .prose .btn {
            @apply inline-block px-5 py-3 my-6 text-white no-underline transition bg-blue-500 rounded shadow;
        }

        .prose .btn:last-child {
            @apply mb-0;
        }

        .prose .checked-list li::marker {
            content: "✓";
        }

        .prose p {
            min-height: 1px;
        }

        .prose .btn:hover {
            @apply text-white shadow-lg;
        }

        @media screen(md) {
            .prose .btn {
                @apply py-4 my-6 px-7;
            }
        }

        .prose .responsive {
            @apply my-8 overflow-hidden rounded;
        }

        .prose .responsive:first-child {
            @apply mt-0;
        }

        .prose .responsive:last-child {
            @apply mb-0;
        }

        .prose>ol li>*:not(:where([class~="not-prose"] *)),
        .prose>ul li>*:not(:where([class~="not-prose"] *)) {
            margin: 0;
        }

        .prose [cols="3"] {
            @apply grid grid-cols-3 gap-8;
        }

        .prose table *:last-child {
            @apply mb-0;
        }

        .prose table *:first-child {
            @apply mt-0;
        }

        .prose :where(tbody th, tfoot th):not(:where([class~="not-prose"] *)):not(:first-child) {
            padding: 0.5714286em;
        }

        @media (min-width: 1024px) {
            .lg\:prose-xl :where(tbody td, tfoot td):not(:where([class~="not-prose"] *)):not(:first-child) {
                padding: 0.8888889em 0.6666667em;
            }
        }

        .bg-fill {
            @apply bg-center bg-cover;
        }

        .fill-parent {
            @apply absolute top-0 left-0 w-full h-full;
        }
    </style>
    <style>
        * {
            position: relative;
        }
    </style>
</head>

<body>
    <div class="bg-primary-500"></div>
    @content
    @if (auth()->user())
        <div class="fixed bottom-0 right-0 z-50 p-4"><a
               href="{{ route('filament.resources.records.edit', ['record' => $record]) }}"
               class="block p-3 text-white rounded-full bg-slate-900">
                <x-heroicon-o-pencil class="w-5 h-5" />
            </a>
        </div>
    @endif
</body>

</html>


<section class="py-16 bg-white sm:py-20 lg:py-24 xl:py-28">
    <div class="container px-6 mx-auto sm:px-8 md:px-10 lg:px-14">
        <h3
            class="mb-4 text-3xl font-bold text-center font-title leading-12 sm:leading-12 md:leading-14 aos-init aos-animate last:mb-0 sm:text-4xl md:mb-8 md:text-5xl">
            Save $24 with yearly billing
        </h3>
        <script async src="https://js.stripe.com/v3/pricing-table.js"></script>
        <stripe-pricing-table pricing-table-id="prctbl_1N46N5F3qCHGIdxq3PdGMy45"
                              publishable-key="pk_live_51N3krSF3qCHGIdxqhTv0kt9h6NF4qM46N2sfDZZv7B22hYvgh1tBrcrlOXhF8CDrLiGi90gAtn3V7JT2TmH96m0w00GRc4iSp2">
        </stripe-pricing-table>
    </div>
</section>
