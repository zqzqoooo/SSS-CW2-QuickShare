@extends('layouts.admin')

@section('header', 'All Files Monitoring (All Files)')

@section('content')
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-400 uppercase bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">File Info</th>
                    <th class="px-6 py-4 text-center">Uploader</th>
                    <th class="px-6 py-4 text-center">Size / Downloads</th>
                    <th class="px-6 py-4 text-center">Expiration Time</th>
                    <th class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($files as $file)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-900 truncate max-w-xs" title="{{ $file->original_name }}">{{ $file->original_name }}</div>
                        <div class="text-xs text-gray-400 font-mono mt-1">{{ $file->share_code }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($file->user)
                            <span class="text-blue-600 font-medium">{{ $file->user->name }}</span>
                        @else
                            <span class="text-gray-400 italic">Guest</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-500 text-center">
                        <div>{{ round($file->file_size / 1024) }} KB</div>
                        <div class="text-xs text-gray-400">{{ $file->download_count }} Downloads</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if(now()->greaterThan($file->expires_at))
                            <span class="text-red-500 font-bold text-xs">Expired</span>
                        @else
                            <span class="text-green-600 font-medium text-xs">{{ $file->expires_at->diffForHumans() }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <form action="{{ route('admin.files.delete') }}" method="POST" onsubmit="return confirm('WARNING: This is a forced deletion operation. The file will be immediately unavailable. Proceed?');">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="id" value="{{ $file->id }}">
                            <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-xs bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-all">
                                Force Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="p-4 border-t border-gray-100">
            {{ $files->links() }}
        </div>
    </div>
@endsection