@extends('layouts.basic')
@section('content')
    <div x-data="wizard" class="min-h-screen py-16 min-w-screen bg-slate-50">
        <div class="container px-8 mx-auto">
            <div class="flex items-center gap-16">
                <div class="w-full md:w-1/2">
                    <div class="p-8 bg-white border rounded-md border-slate-200">
                        <ul>
                            <template x-for="step in steps">
                                <li x-show="step.active">
                                    <div class="">
                                        <h3 class="flex items-center justify-between mb-8 text-2xl">
                                            <span x-text="step.title"></span>
                                            {{-- <span x-text="total" class="text-lg text-slate-500"></span> --}}
                                        </h3>
                                        <ul class="">
                                            <template x-for="opt in step.options">
                                                <li>
                                                    <div @click.prevent="opt.active = !opt.active"
                                                         class="flex gap-4 my-6 cursor-pointer group">
                                                        <div class="pt-1">
                                                            <div :class="opt.active ? 'bg-primary-500 text-white scale-[1.05]' :
                                                                ' text-slate-100'"
                                                                 class="w-8 transition rounded-full bg-slate-100"
                                                                 style="padding-top: 100%">
                                                                <div :class="opt.active ? 'scale-[1.05]' : 'scale-[0.85]'"
                                                                     class="absolute top-0 left-0 flex items-center justify-center w-full h-full transition transform">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                         viewBox="0 0 24 24" fill="currentColor"
                                                                         class="w-4 h-4"
                                                                         :class="opt.active ? 'text-white' :
                                                                             ' group-hover:text-primary'">
                                                                        <path
                                                                              d="M7.493 18.75c-.425 0-.82-.236-.975-.632A7.48 7.48 0 016 15.375c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75 2.25 2.25 0 012.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H14.23c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23h-.777zM2.331 10.977a11.969 11.969 0 00-.831 4.398 12 12 0 00.52 3.507c.26.85 1.084 1.368 1.973 1.368H4.9c.445 0 .72-.498.523-.898a8.963 8.963 0 01-.924-3.977c0-1.708.476-3.305 1.302-4.666.245-.403-.028-.959-.5-.959H4.25c-.832 0-1.612.453-1.918 1.227z" />
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div :class="opt.active ? 'text-primary-800' : ''"
                                                                 class="text-lg" x-text="opt.title">
                                                            </div>
                                                            <div class="text-sm text-slate-900/50" x-text="opt.desc"></div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </li>
                            </template>
                        </ul>
                        <div class="flex justify-center gap-4 pt-8">
                            <a :class="currStep > 0 ? '' : 'pointer-events-none opacity-25'"
                               class="flex justify-center w-1/2 gap-2 py-4 text-center transition rounded hover:bg-primary bg-slate-100 hover:text-white"
                               href="#" @click.prevent="prevStep">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                     class="w-6 h-6">
                                    <path fill-rule="evenodd"
                                          d="M11.03 3.97a.75.75 0 010 1.06l-6.22 6.22H21a.75.75 0 010 1.5H4.81l6.22 6.22a.75.75 0 11-1.06 1.06l-7.5-7.5a.75.75 0 010-1.06l7.5-7.5a.75.75 0 011.06 0z"
                                          clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a :class="currStep < steps.length - 1 ? '' : 'pointer-events-none opacity-25'"
                               class="flex justify-center w-1/2 gap-2 py-4 text-center transition rounded hover:bg-primary bg-slate-100 hover:text-white"
                               href="#" @click.prevent="nextStep">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                     class="w-6 h-6">
                                    <path fill-rule="evenodd"
                                          d="M12.97 3.97a.75.75 0 011.06 0l7.5 7.5a.75.75 0 010 1.06l-7.5 7.5a.75.75 0 11-1.06-1.06l6.22-6.22H3a.75.75 0 010-1.5h16.19l-6.22-6.22a.75.75 0 010-1.06z"
                                          clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                        <div class="absolute bottom-0 left-0 w-full p-2">
                            <div class="h-1 transition-all rounded-full bg-primary"
                                 :style="{ width: (((currStep + 1) / steps.length) * 100) + '%' }">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full text-center md:w-1/2">
                    <div class="flex items-center gap-8">
                        <h3 x-text="total" class="my-4 text-5xl font-bold text-primary-900"></h3>
                        <div class="flex gap-6 text-sm">
                            <div>
                                <div x-show="activeItems.length > 0" class="w-px h-full bg-slate-300">
                                    <div
                                         class="w-4 h-4 transform -rotate-45 -translate-x-1/2 -translate-y-1/2 border-t border-l left-1/2 top-1/2 border-slate-300 bg-slate-50">
                                    </div>
                                </div>
                            </div>
                            <ul class="py-4">
                                <template x-for="opt in activeItems">
                                    <li class="flex items-center gap-2 py-1">
                                        <span class="text-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                                 class="w-5 h-5">
                                                <path fill-rule="evenodd"
                                                      d="M19.916 4.626a.75.75 0 01.208 1.04l-9 13.5a.75.75 0 01-1.154.114l-6-6a.75.75 0 011.06-1.06l5.353 5.353 8.493-12.739a.75.75 0 011.04-.208z"
                                                      clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                        <span x-text="opt.title"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
        });
        document.addEventListener('alpine:init', () => {
            Alpine.data('wizard', () => ({
                init() {
                    this.$watch('steps', () => {
                        this.total = 1085;
                        this.activeItems = [];
                        this.steps.forEach(step => {
                            step.options.forEach(opt => {
                                if (opt.active) {
                                    this.activeItems.push(opt);
                                    if (opt.cost) {
                                        this.total += opt.cost
                                    }
                                }
                            })
                        })
                        this.total = formatter.format(this.total)
                    });
                },
                nextStep() {
                    this.changeStep(this.currStep + 1);
                },
                prevStep() {
                    this.changeStep(this.currStep - 1);
                },
                changeStep(target) {
                    this.steps.forEach((step, i) => {
                        step.active = false;
                        console.log(i == target, i, target)
                        if (i == target) {
                            step.active = true;
                        }
                    });
                    this.currStep = target;
                },
                total: formatter.format(1085),
                currStep: 0,
                activeItems: [],
                steps: [{
                        title: 'Branding & Visuals',
                        active: true,
                        options: [{
                                title: 'Logo Design',
                                active: false,
                                cost: 267,
                                desc: 'Create logo for website project'
                            },
                            {
                                title: 'Photoshoot',
                                cost: 163,
                                active: false,
                                desc: 'Onsite photoshoot to capture needed media '
                            },
                            {
                                title: 'Videoshoot',
                                cost: 195,
                                active: false,
                                desc: 'Onsite videoshoot to capture needed media '
                            },
                            {
                                title: 'Video Editing',
                                cost: 104,
                                active: false,
                                desc: 'Does the project require video editing?'
                            },
                            {
                                title: 'Custom Motion Graphics',
                                cost: 260,
                                active: false,
                                desc: 'Creating engaging content that impresses'
                            },
                        ]
                    },
                    {
                        title: 'Web Design (not working)',
                        active: false,
                        options: [{
                                title: 'Page Template Design',
                                active: false,
                                desc: 'How important is design on this project?'
                            },
                            {
                                title: 'Unique Page Templates',
                                active: false,
                                desc: 'How many Unique Page Templates are there?'
                            },
                            {
                                title: 'Basic Web Design',
                                active: false,
                                desc: 'Onsite videoshoot to capture needed media '
                            },
                            {
                                title: 'Standard Web Design',
                                active: false,
                                desc: 'Does the project require video editing?'
                            },
                            {
                                title: 'Premium Web Design',
                                active: false,
                                desc: 'Creating engaging content that impresses'
                            },
                        ]
                    },
                    {
                        title: 'Add-ons',
                        active: false,
                        options: [{
                                title: 'Search Engine Optimization',
                                active: false,
                                cost: 809,
                                desc: 'Increase the quantity and quality of traffic to your site from organic search engine results.'
                            },
                            {
                                title: 'E-commerce / Online Sales',
                                cost: 2113,
                                active: false,
                                desc: 'Offer the convenience to customers to purchase products from anywhere at any time.'
                            },
                            {
                                title: 'Conversion Tracking',
                                cost: 130,
                                active: false,
                                desc: 'Determine how effective your site is at driving conversions, such as purchases, sign-ups, or leads.'
                            },
                            {
                                title: 'Newsletter & Live Chat',
                                cost: 98,
                                active: false,
                                desc: 'Stay in touch with visitors through email newsletters or directly through live chat.'
                            },
                            {
                                title: 'ADA Compliance',
                                cost: 686,
                                active: false,
                                desc: 'Ensure visitors with disabilities can access and use your website effectively.'
                            },
                        ]
                    }
                ]
            }))
        })
    </script>
@endsection
