@extends('layouts.admin')

@section('header', 'System Overview (Dashboard)')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Total Uploads (History)</div>
            <div class="text-3xl font-black text-gray-900">{{ $totalUploads }} <span class="text-lg font-medium text-gray-400">files</span></div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Cumulative Bandwidth</div>
            <div class="text-3xl font-black text-blue-600">{{ $totalSize }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Registered User Ratio</div>
            <div class="text-3xl font-black text-purple-600">{{ $userRatio }}%</div>
        </div>
        <a href="{{ route('admin.users') }}" class="bg-black text-white p-6 rounded-2xl shadow-lg hover:bg-gray-800 transition-colors flex flex-col justify-center items-center text-center group">
            <svg class="w-8 h-8 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <span class="font-bold text-sm">Manage Users &rarr;</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
            <h3 class="font-bold text-gray-800">Latest Active Files</h3>
        </div>
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-400 uppercase bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3">File Name</th>
                    <th class="px-6 py-3 text-center">Size</th>
                    <th class="px-6 py-3 text-center">Uploader</th>
                    <th class="px-6 py-3 text-center">Upload Time</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($recentFiles as $file)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $file->original_name }}</td>
                    <td class="px-6 py-4 text-gray-500 text-center">{{ round($file->file_size / 1024) }} KB</td>
                    <td class="px-6 py-4 text-center">
                        @if($file->user)
                            <span class="bg-blue-50 text-blue-600 px-2 py-1 rounded text-xs font-bold">{{ $file->user->name }}</span>
                        @else
                            <span class="bg-gray-100 text-gray-500 px-2 py-1 rounded text-xs font-bold">Guest</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-400 text-center">{{ $file->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection