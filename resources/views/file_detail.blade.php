<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/svg+xml" href="/logo.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $file->original_name }} - Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F5F5F7; }
        .glass { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(16px); border-bottom: 1px solid rgba(0, 0, 0, 0.05); }
        [x-cloak] { display: none !important; }
        /* Remove default input focus style */
        input:focus { outline: none; }
    </style>
</head>
<body class="antialiased text-gray-900 min-h-screen flex flex-col">

    <nav class="fixed w-full z-50 top-0 start-0 glass transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            
            <div class="flex items-center gap-4 overflow-hidden mr-4">
                <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-black transition-colors flex items-center text-sm font-bold group whitespace-nowrap bg-white/50 px-3 py-2 rounded-full hover:bg-white border border-transparent hover:border-gray-200">
                    <svg class="w-4 h-4 mr-1.5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back
                </a>
                <div class="h-6 w-px bg-gray-300 shrink-0"></div>
                <span class="font-bold text-gray-900 truncate text-lg" title="{{ $file->original_name }}">{{ $file->original_name }}</span>
            </div>

            <div class="relative shrink-0" x-data="{ open: false }" x-cloak>
                <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100/80 transition-colors focus:outline-none group">
                    <span class="text-sm font-bold text-gray-700 group-hover:text-black">{{ Auth::user()->name }}</span>
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-2"
                     class="absolute right-0 mt-2 w-60 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden ring-1 ring-black ring-opacity-5">
                    
                    <div class="px-5 py-3 border-b border-gray-50 bg-gray-50/50">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Signed in as</p>
                        <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->email }}</p>
                    </div>

                    <div class="p-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center px-4 py-2 text-sm font-bold text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow pt-14 pb-12 px-4 mt-8 mb-4 sm:px-6">
        <div class="max-w-[95%] 2xl:max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">

            <div class="lg:col-span-4 space-y-6 order-2 lg:order-1">
                
                <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-200/60 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full blur-3xl -mr-10 -mt-10 opacity-50 pointer-events-none"></div>
                    
                    <div class="text-center mb-8">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 block">Share Code</label>
                        <div onclick="copyCode()" class="text-5xl font-mono font-black text-blue-600 tracking-widest cursor-pointer hover:scale-105 transition-transform" title="Click to copy">
                            {{ $file->share_code }}
                        </div>
                        <p id="copy-msg" class="text-xs text-green-600 mt-2 font-bold opacity-0 transition-opacity">Copied!</p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                            <span class="text-sm text-gray-500 font-medium">File Size</span>
                            <span class="text-sm font-bold text-gray-900">{{ round($file->file_size / 1024, 1) }} KB</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                            <span class="text-sm text-gray-500 font-medium">Download Count</span>
                            <span class="text-sm font-bold text-gray-900">{{ $file->download_count }} times</span>
                        </div>
                        <div class="flex justify-between items-center pt-1">
                            <span class="text-sm text-gray-500 font-medium">Expiration Time</span>
                            <span class="text-sm font-bold {{ now()->greaterThan($file->expires_at) ? 'text-red-500' : 'text-green-600' }}">
                                {{ $file->expires_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-200/60">
                    <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        Rename File
                    </h3>
                    
                    <form action="{{ route('user.files.update', $file->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <input type="text" name="filename" value="{{ $file->original_name }}" 
                               class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-black focus:border-transparent p-3 font-bold transition-all mb-3" 
                               required>
                        
                        <button type="submit" class="w-full bg-black text-white hover:bg-gray-800 mt-2 px-4 py-3 rounded-2xl text-sm font-bold transition-colors">
                            Save
                        </button>
                    </form>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <form action="{{ route('user.files.update', $file->id) }}" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="extend" value="1">
                        <button type="submit" class="w-full bg-white border border-gray-200 hover:border-blue-300 hover:bg-blue-50 text-blue-600 py-4 rounded-[1.5rem] text-sm font-bold transition-all flex flex-col items-center justify-center gap-2 group shadow-sm hover:shadow-md h-24">
                            <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Extend 3 Days</span>
                        </button>
                    </form>

                    <form action="{{ route('user.files.destroy', $file->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this file? This action cannot be undone.');">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full bg-white border border-gray-200 hover:border-red-300 hover:bg-red-50 text-red-600 py-4 rounded-[1.5rem] text-sm font-bold transition-all flex flex-col items-center justify-center gap-2 group shadow-sm hover:shadow-md h-24">
                            <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            <span>Delete File</span>
                        </button>
                    </form>
                </div>
            </div>

                        <div class="lg:col-span-8 space-y-6 order-1 lg:order-2">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-200/60 overflow-hidden h-full min-h-[600px] flex items-center justify-center relative bg-grid-pattern">
                    
                    @php
                        $ext = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
                        $previewUrl = route('user.files.preview', $file->id);
                    @endphp

                    @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']))
                        <img src="{{ $previewUrl }}" class="w-full h-full object-contain max-h-[800px] p-4">
                    
                    @elseif(in_array($ext, ['mp4', 'mov', 'webm']))
                        <video controls class="w-full max-h-[800px] bg-black rounded-2xl shadow-2xl">
                            <source src="{{ $previewUrl }}" type="video/{{ $ext }}">
                        </video>
                    
                    @elseif(in_array($ext, ['mp3', 'wav', 'ogg']))
                        <div class="w-full p-20 text-center">
                            <div class="w-40 h-40 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-10 text-blue-500 shadow-inner animate-pulse-slow">
                                <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path></svg>
                            </div>
                            <div class="bg-gray-100 rounded-full p-4 shadow-inner max-w-lg mx-auto">
                                <audio controls class="w-full"><source src="{{ $previewUrl }}" type="audio/{{ $ext }}"></audio>
                            </div>
                        </div>
                    
                    @else
                        <div class="text-center p-16">
                            <div class="w-32 h-32 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-6 text-gray-400 shadow-sm">
                                <span class="text-4xl font-black uppercase tracking-wider">{{ $ext }}</span>
                            </div>
                            <p class="text-gray-500 font-medium text-lg">Online preview is not supported for this file type</p>
                            <a href="{{ route('file.download', ['code' => $file->share_code]) }}" class="mt-4 mb-4 inline-flex items-center gap-2 bg-black text-white px-4 py-4 rounded-full font-bold text-base hover:bg-gray-800 transition-all hover:-translate-y-1 shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Download to view locally
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    @if (session('status'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             class="fixed bottom-8 right-8 bg-black/80 backdrop-blur-md text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 z-50 border border-white/10">
            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-bold text-sm">{{ session('status') }}</span>
        </div>
    @endif

    <script>
        function copyCode() {
            const code = "{{ $file->share_code }}";
            navigator.clipboard.writeText(code).then(() => {
                const msg = document.getElementById('copy-msg');
                msg.classList.remove('opacity-0');
                setTimeout(() => msg.classList.add('opacity-0'), 2000);
            });
        }
    </script>

</body>
</html>