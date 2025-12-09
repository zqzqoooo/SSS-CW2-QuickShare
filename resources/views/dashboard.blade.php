<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/svg+xml" href="/icon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Files - QuickShare</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F5F5F7; }
        .glass { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(0, 0, 0, 0.05); }
        .smooth-transition { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased text-gray-900">

    <nav class="fixed w-full z-50 top-0 start-0 glass border-b border-gray-200/50">
        <div class="max-w-6xl mx-auto flex flex-wrap items-center justify-between px-6 py-4">
            <a href="/" class="flex items-center space-x-2 rtl:space-x-reverse">
                <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center text-white font-bold text-lg">Q</div>
                <span class="self-center text-xl font-semibold whitespace-nowrap tracking-tight">QuickShare</span>
            </a>
            
            <div class="flex items-center md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse relative" x-data="{ open: false }">
                @if (Route::has('login'))
                    @auth
                        <div class="relative" x-data="{ open: false }" x-cloak>
                            
                            <button @click="open = !open" @click.away="open = false" class="flex items-center space-x-2 gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors duration-200 focus:outline-none group">
                                <span class="flex pr-2 text-xl font-semibold text-gray-800 group-hover:text-black tracking-tight">
                                    {{ Auth::user()->name}}
                                </span>
                                <svg :class="{'rotate-180': open}" class="w-4 h-4 text-gray-500 group-hover:text-gray-800 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 translate-y-2"
                                 class="absolute right-0 mt-2 w-72 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 origin-top-right overflow-hidden ring-1 ring-black ring-opacity-5"
                                 style="width: 200px"
                                 >

                                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/30">
                                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Signed in as</p>
                                    <p class="text-base font-bold text-gray-900 truncate" title="{{ Auth::user()->email }}">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate mt-0.5">{{ Auth::user()->email }}</p>
                                </div>

                                <div class="py-2 px-2">
                                    @if(Auth::user()->is_admin)
                                    <a href="{{ url('/admin') }}" class="flex w-full items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-red-50 rounded-xl smooth-transition">
                                        <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-blue-500 smooth-transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                         Admin Dashboard
                                    </a>
                                    @endif

                                    <a href="{{ url('/dashboard') }}" class="flex w-full items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-red-50 rounded-xl smooth-transition">
                                        <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-blue-500 smooth-transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path></svg>
                                         My File Management
                                    </a>
                                    
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-50 rounded-xl smooth-transition">
                                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                            Log Out
                                        </button>
                                    </form>
                                </div>

                                </div>
                        </div>
                    @else
                        <div class="flex space-x-2">
                            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-500 hover:text-black smooth-transition px-3 py-2">Log in</a>
                            <a href="{{ route('register') }}" class="text-white bg-black hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 text-center smooth-transition">Register</a>
                        </div>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <main class="flex-grow pt-8 pb-10 px-6 mt-8 mb-6">
        <div class="max-w-6xl mx-auto">
            
            <div class="flex justify-between items-end mt-8 mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">My Files</h1>
                    <p class="text-gray-500 mt-1 text-sm">Manage your cloud assets</p>
                </div>
                <a href="/" class="bg-black text-white hover:bg-gray-800 px-6 py-3 rounded-full font-bold text-sm transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Upload New File
                </a>
            </div>

            @if (session('status'))
                <div class="mb-6 bg-green-50 border border-green-100 text-green-700 px-4 py-3 rounded-xl text-sm font-medium shadow-sm">
                    ✅ {{ session('status') }}
                </div>
            @endif

            @if($files->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 space-y-1">
                    @foreach($files as $file)
                        <a href="{{ route('user.files.show', $file->id) }}" class="block group">
                            <div class="bg-white rounded-2xl px-6 py-4 border border-gray-100 shadow-lg hover:shadow-xl smooth-transition hover:-translate-y-0.5 hover:border-blue-300 h-full flex flex-col">
                                
                                <div class="flex justify-between items-start mb-2">
                                    <div class="w-14 h-14 rounded-2xl bg-gray-100 px-4 flex items-center justify-center text-gray-700 font-black border-2 border-gray-200 group-hover:bg-blue-100 group-hover:text-blue-700 transition-colors">
                                        @php $ext = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION)); @endphp
                                        <span class="font-black text-xs uppercase">{{ $ext }}</span>
                                    </div>
                                    
                                    @if(now()->greaterThan($file->expires_at))
                                        <span class="bg-red-100 text-red-700 text-xs px-3 py-1.5 rounded-full font-bold">Expired</span>
                                    @else
                                        <span class="bg-green-100 text-green-700 text-xs px-3 py-1.5 rounded-full font-bold group-hover:bg-green-200">
                                            Expires {{ $file->expires_at->diffForHumans(null, true) }}
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-gray-900 truncate mb-2 group-hover:text-blue-600 transition-colors font-bold">{{ $file->original_name }}</h3>
                                
                                <div class="text-sm font-mono bg-gray-100 text-gray-800 px-3 py-2 rounded-lg font-extrabold text-center tracking-widest mb-2">{{ $file->share_code }}</div>

                                <div class="flex justify-between items-center pt-3 mt-auto">
                                    <p class="text-xs text-gray-400 font-medium">{{ round($file->file_size / 1024) }} KB</p>
                                    <p class="text-xs text-gray-500 font-medium">Downloads: {{ $file->download_count }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-24">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    <h3 class="text-gray-900 font-medium">No Files Yet</h3>
                    <p class="text-gray-500 text-sm mt-1">Click 'Upload New File' in the top right to upload your first file</p>
                </div>
            @endif

        </div>
    </main>

</body>
</html>