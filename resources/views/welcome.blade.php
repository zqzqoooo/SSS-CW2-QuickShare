<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/svg+xml" href="/logo.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QuickShare - Minimalist Transfer</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #FBFBFD; /* Common background gray on Apple's website */ }
        /* Custom smooth transition */
        .smooth-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        /* Frosted glass effect */
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.5); }
        /* Cursor blink animation */
        .cursor-blink { animation: blink 1s step-end infinite; }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }
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
                            <a href="{{ route('register') }}" class="text-white bg-black hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 text-center smooth-transition">Sign up</a>
                        </div>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <div class="relative isolate px-6 pt-14 lg:px-8">
        
        <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
            <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
        </div>

        <div class="mx-auto max-w-3xl py-24 sm:py-32 text-center" 
            x-data="{ text: '', textToType: 'That\'s all right.' }" 
            x-init="setTimeout(() => { let i = 0; setInterval(() => { if (i < textToType.length) { text += textToType.charAt(i); i++; } }, 40) }, 300)">
            
            <h1 class="text-5xl font-bold tracking-tight text-gray-900 sm:text-7xl mb-6">
                Transmission,<br>
                <span class="text-gray-400" x-text="text"></span><span class="text-gray-400 cursor-blink">|</span>
            </h1>

            <p class="mt-6 text-lg leading-8 text-gray-600 font-light">
                No cables, no complex setup required. Enter the 6-digit code and files flow instantly between devices.<br>
                @auth
                    <span class="inline-block mt-2 px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-semibold tracking-wide uppercase">Premium Membership Active</span>
                @else
                    <span class="inline-block mt-2 px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-semibold tracking-wide uppercase">Guest Mode: Limit {{config('quickshare.upload_limits.guest')/1024/1024}}MB / {{config('quickshare.expiration_days.guest')}} Days</span>
                @endauth
            </p>
        </div>

        <div class="mx-auto max-w-5xl grid grid-cols-1 md:grid-cols-2 gap-8 pb-24">
            
            <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-8 md:p-12 hover:shadow-2xl hover:-translate-y-1 smooth-transition border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900">Send File</h2>
                    <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    </div>
                </div>

                @if ($errors->has('file') || $errors->has('msg'))
                    <div class="mb-5 p-4 bg-red-50 rounded-2xl border border-red-100 flex items-start gap-3 relative z-10 animate-pulse">
                        <svg class="w-5 h-5 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="text-sm text-red-600 font-medium">
                            {{ $errors->first('file') ?: $errors->first('msg') }}
                        </div>
                    </div>
                @endif

                <form action="{{ route('file.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div class="relative group">
                    <input type="file" name="file" id="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required onchange="showFileName(this)">
                    
                    <div class="border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center group-hover:border-blue-400 group-hover:bg-blue-50/30 smooth-transition">
                        <div class="text-gray-400 mb-2 group-hover:text-blue-500">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        </div>
                        <p id="file-label" class="text-sm font-medium text-gray-900">Click to select or drag and drop files here</p>
                        <p class="text-xs text-gray-500 mt-1">
                            @auth Max supported size {{config('quickshare.upload_limits.user')/1024/1024}}MB 
                            @else Max supported size {{config('quickshare.upload_limits.guest')/1024/1024}}MB 
                            @endauth
                        </p>
                    </div>
                </div>

                @auth
                    <div class="flex items-center">
                        <input id="is_one_time" name="is_one_time" type="checkbox" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                        <label for="is_one_time" class="ms-2 text-sm font-medium text-gray-600">Enable "Self-Destruct" (Destroy after 1 download)</label>
                    </div>
                    @endauth

                    <button id="upload-btn" type="submit" class="w-full text-white bg-black hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 font-medium rounded-2xl text-lg px-5 py-3.5 text-center smooth-transition shadow-lg shadow-gray-900/20">
                        Start Upload
                    </button>
                </form>
            </div>

            <div class="bg-black text-white rounded-3xl shadow-xl shadow-gray-400/20 p-8 md:p-12 flex flex-col justify-center relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-gray-800 rounded-full blur-3xl opacity-50"></div>

                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-2xl font-semibold">Receive File</h2>
                        <div class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </div>
                    </div>

                    @if ($errors->has('code') || $errors->has('download_msg'))
                        <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-2xl flex items-start gap-3 animate-pulse">
                            <svg class="w-5 h-5 text-red-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <div>
                                <h3 class="text-sm font-bold text-red-400">Download Failed</h3>
                                <p class="text-xs text-red-300 mt-1">
                                    {{ $errors->first('code') ?: $errors->first('download_msg') }}
                                </p>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('file.download') }}" method="GET" class="space-y-6">
                        <div>
                            <label for="code" class="block mb-2 text-sm font-medium text-gray-400">Enter 6-digit Code</label>
                            <input type="text" name="code" id="code" placeholder="A1B2C3" maxlength="6" required 
                                class="bg-gray-900 border border-gray-800 text-white text-3xl font-bold rounded-2xl focus:ring-blue-500 focus:border-blue-500 block w-full p-4 text-center tracking-widest uppercase placeholder-gray-700"
                                style="font-family: monospace;">
                        </div>

                        <button type="submit" class="w-full text-black bg-white hover:bg-gray-100 focus:ring-4 focus:ring-gray-500 font-medium rounded-2xl text-lg px-5 py-3.5 text-center smooth-transition">
                            Download Now
                        </button>
                    </form>
                    
                    <p class="mt-3 text-xs text-gray-500 text-center">
                        * Files will be automatically destroyed after expiration or if set to "self-destruct"
                    </p>
                </div>
            </div>

        </div>
    </div>

    <footer class="text-center py-10 text-gray-400 text-sm border-t border-gray-200 bg-white">
        <p>&copy; {{ date('Y') }} QuickShare. Designed with simplicity.</p>
    </footer>

    <script>
        // Get the current user's upload limit (in bytes)
        // Logged-in user: 50MB (52428800), Guest: 5MB (5242880)
        // Before modification:
        // const MAX_SIZE = {{ Auth::check() ? 52428800 : 5242880 }};

        // After modification:
        const MAX_SIZE = {{ Auth::check() ? config('quickshare.upload_limits.user') : config('quickshare.upload_limits.guest') }};        
        
        function showFileName(input) {
            const label = document.getElementById('file-label');
            const submitBtn = document.getElementById('upload-btn'); // Remember to add this id to the submit button
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileName = file.name;
                const fileSize = file.size;

                // Core logic: Check file size
                if (fileSize > MAX_SIZE) {
                    // Calculate MB for display convenience
                    const maxMB = Math.floor(MAX_SIZE / 1024 / 1024);
                    label.innerHTML = `<span class="text-red-600 font-bold">❌ File Too Large!</span> (Limit ${maxMB}MB)`;
                    label.classList.remove('text-gray-900');
                    
                    // Clear input and disable button
                    input.value = ''; 
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    // Size is normal
                    label.innerHTML = `<span class="text-blue-600 font-semibold">✅ Selected:</span> ${fileName}`;
                    label.classList.add('text-gray-900');
                    
                    // Enable button
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        }
    </script>
</body>
</html>