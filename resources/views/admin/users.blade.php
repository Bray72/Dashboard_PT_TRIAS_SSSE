@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">

    <!-- HEADER -->
    <div class="mb-6">
        <h1 class="text-4xl font-bold text-blue-900 dark:text-blue-400">Detail User</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Daftar Semua User</p>
    </div>

    <!-- ALERT -->
    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- TABLE APPROVAL -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($pendingUsers as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-800">
                                {{ $user->name }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
                                    Pending
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form method="POST" action="{{ route('admin.users.approve', $user->id) }}">
                                    @csrf
                                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        Approve
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-500">
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
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">
                User Pending ({{ $pendingUsers->count() }})
            </h3>

            <div class="space-y-3 max-h-64 overflow-y-auto">
                @forelse ($pendingUsers as $user)
                    <div class="flex justify-between items-center border rounded-lg p-3">
                        <div>
                            <p class="font-medium">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        </div>
                        <span class="text-xs bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full">
                            Pending
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Kosong</p>
                @endforelse
            </div>
        </div>

        <!-- APPROVED -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">
                User Approved ({{ $approvedUsers->count() }})
            </h3>

            <div class="space-y-3 max-h-64 overflow-y-auto">
                @forelse ($approvedUsers as $user)
                    <div class="flex justify-between items-center border rounded-lg p-3">
                        <div>
                            <p class="font-medium">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        </div>
                        <span class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full">
                            Approved
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada</p>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
