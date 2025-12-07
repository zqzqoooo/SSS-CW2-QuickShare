<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/svg+xml" href="/logo.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Upload Successful - QuickShare</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; background-color: #F5F5F7; } .smooth-transition { transition: all 0.2s ease-in-out; }</style>
</head>
<body class="flex items-center justify-center min-h-screen px-4">

    <div class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl shadow-gray-200/50 overflow-hidden border border-white">
        
        <div class="bg-gradient-to-br from-green-400 to-emerald-600 p-8 text-center relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
            
            <div class="relative z-10">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center mx-auto mb-4 border border-white/30 shadow-inner">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h2 class="text-2xl font-bold text-white tracking-tight">File Upload Successful</h2>
                <p class="text-green-50 text-sm mt-1 font-medium opacity-90">Ready to share</p>
            </div>
        </div>

        <div class="p-10">
            
            <div class="text-center mb-10">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 block">Your Share Code</label>
                <div class="bg-blue-50 border border-blue-100 rounded-3xl py-8 px-4 relative group cursor-pointer hover:bg-blue-100 transition-colors" onclick="copyCode()">
                    <div class="text-7xl font-black text-blue-600 tracking-widest font-mono group-hover:scale-105 transition-transform duration-300" style="letter-spacing: 0.1em;">
                        {{ $file->share_code }}
                    </div>
                    <div class="absolute top-2 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                        <span class="text-[10px] bg-blue-200 text-blue-700 px-2 py-1 rounded-full font-bold">Click to Copy</span>
                    </div>
                    <p id="copy-msg" class="absolute bottom-2 left-0 right-0 text-center text-xs text-green-600 font-bold opacity-0 transition-opacity">Copied!</p>
                </div>
            </div>

            <div class="space-y-5">
                
                <div class="flex items-center justify-between border-b border-gray-50 pb-4">
                    <div class="flex items-center gap-4 overflow-hidden">
                        <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center shrink-0 border border-gray-100">
                            @php $ext = pathinfo($file->original_name, PATHINFO_EXTENSION); @endphp
                            <span class="text-xs font-black text-gray-400 uppercase tracking-tight">{{ substr($ext, 0, 4) }}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-0.5">File Name</p>
                            <p class="font-bold text-gray-900 truncate text-lg leading-tight" title="{{ $file->original_name }}">{{ $file->original_name }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-0.5">Expires In</p>
                        <p class="font-bold text-lg {{ now()->greaterThan($file->expires_at) ? 'text-red-500' : 'text-green-600' }}">
                            {{ $file->expires_at->diffForHumans() }}
                        </p>
                    </div>
                    
                    @if($file->is_one_time)
                        <div class="bg-red-50 border border-red-100 px-4 py-2 rounded-xl flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                            <span class="text-xs font-bold text-red-600">View Once</span>
                        </div>
                    @else
                        <div class="bg-gray-50 border border-gray-100 px-4 py-2 rounded-xl">
                            <span class="text-xs font-bold text-gray-500">Standard Storage</span>
                        </div>
                    @endif
                </div>

            </div>

            <a href="/" class="block w-full bg-black text-white hover:bg-gray-800 font-bold rounded-2xl text-lg px-5 py-4 text-center mt-10 shadow-xl shadow-gray-200 hover:-translate-y-1 smooth-transition">
                Go to Homepage
            </a>
        </div>
    </div>

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