@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">

    <!-- HEADER -->
    <div class="mb-6">
        <h1 class="text-4xl font-bold text-blue-900 dark:text-blue-400">Detail User</h1>
        <p class="text-gray-600 dark:text-gray-300 mt-2">Daftar Semua User</p>
    </div>

    <!-- ALERT -->
    @if (session('success'))
        <div class="mb-4 bg-green-100 dark:bg-green-900 border border-green-300 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- TABLE APPROVAL -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Aksi</th>
                    </tr>
                </thead>

                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($pendingUsers as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-4 font-medium text-gray-800 dark:text-gray-100">
                                {{ $user->name }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-200">
                                    Pending
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex gap-2 justify-center">
                                    <form method="POST" action="{{ route('admin.users.approve', $user->id) }}" style="display:inline;">
                                        @csrf
                                        <button class="px-4 py-2 bg-blue-600 dark:bg-blue-700 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition">
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.reject', $user->id) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-4 py-2 bg-red-600 dark:bg-red-700 text-white rounded-lg hover:bg-red-700 dark:hover:bg-red-600 transition" onclick="return confirm('Apakah Anda yakin ingin me-reject user ini?')">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-500 dark:text-gray-400">
                                Tidak ada user pending
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- DETAIL USER -->
    <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- PENDING -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">
                User Pending ({{ $pendingUsers->count() }})
            </h3>

            <div class="space-y-3 max-h-64 overflow-y-auto">
                @forelse ($pendingUsers as $user)
                    <div class="flex justify-between items-center border border-gray-200 dark:border-gray-700 rounded-lg p-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                        </div>
                        <span class="text-xs bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-200 px-3 py-1 rounded-full">
                            Pending
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kosong</p>
                @endforelse
            </div>
        </div>

        <!-- APPROVED -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">
                User Approved ({{ $approvedUsers->count() }})
            </h3>

            <div class="space-y-3 max-h-64 overflow-y-auto">
                @forelse ($approvedUsers as $user)
                    <div class="flex justify-between items-center border border-gray-200 dark:border-gray-700 rounded-lg p-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                        </div>
                        <div class="flex gap-2 items-center">
                            <span class="text-xs bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200 px-3 py-1 rounded-full">
                                Approved
                            </span>
                            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="text-xs px-3 py-1 bg-red-600 dark:bg-red-700 text-white rounded hover:bg-red-700 dark:hover:bg-red-600 transition" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada</p>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
