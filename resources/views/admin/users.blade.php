@extends('layouts.admin')

@section('header', 'User Management')

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm text-left">
        <thead class="text-xs text-gray-400 uppercase bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="px-6 py-4 text-center">ID</th>
                
                <th class="px-6 py-4 text-center">User</th>
                
                <th class="px-6 py-4 text-center">Email</th>
                
                <th class="px-6 py-4 text-center">Status</th>
                
                <th class="px-6 py-4 text-center">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($users as $user)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 text-gray-400 text-center">#{{ $user->id }}</td>
                
                <td class="px-6 py-4 text-center">
                    <div class="flex items-center gap-3 text-center justify-center">
                        <span class="font-bold text-gray-900">{{ $user->name }}</span>
                    </div>
                </td>
                
                <td class="px-6 py-4 text-gray-500 text-center">{{ $user->email }}</td>
                
                <td class="px-6 py-4 text-center">
                    @if($user->is_banned)
                        <span class="bg-red-50 text-red-600 px-2.5 py-1 rounded-full text-xs font-bold">Banned</span>
                    @else
                        <span class="bg-green-50 text-green-600 px-2.5 py-1 rounded-full text-xs font-bold">Normal</span>
                    @endif
                </td>
                
                <td class="px-6 py-4 text-center">
                    @if($user->is_admin)
                        <span class="text-gray-300 text-xs italic">Admin</span>
                    @else
                        <div class="flex justify-center">
                            <form action="{{ route('admin.users.ban') }}" method="POST">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                @if($user->is_banned)
                                    <button type="submit" class="text-green-600 hover:text-green-800 font-bold text-xs border border-green-200 hover:bg-green-50 px-3 py-1.5 rounded-lg transition-all">
                                        🔓 Unban
                                    </button>
                                @else
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-xs border border-red-200 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-all" onclick="return confirm('Are you sure you want to ban this user?')">
                                        🚫 Ban
                                    </button>
                                @endif
                            </form>
                        </div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection