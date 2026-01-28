@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">

    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Persetujuan User
        </h2>
        <p class="text-sm text-gray-500">
            Daftar user yang menunggu persetujuan admin
        </p>
    </div>

    <!-- Alert Success -->
    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 border border-green-300 text-green-700 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <!-- Card -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                            Nama
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                            Status
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-800 font-medium">
                                {{ $user->name }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $user->email }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                                    {{ $user->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <form method="POST" action="{{ route('admin.users.approve', $user->id) }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium
                                               text-white bg-blue-600 rounded-lg hover:bg-blue-700
                                               focus:outline-none focus:ring-2 focus:ring-blue-400">
                                        Approve
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-6 text-center text-gray-500">
                                Tidak ada user yang menunggu persetujuan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- USER PENDING -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        ⏳ User Pending ({{ $pendingUsers->count() }})
                    </h3>

                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @forelse ($pendingUsers as $user)
                            <div class="flex justify-between items-center border rounded-lg p-3 hover:bg-gray-50">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                </div>
                                <span class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-yellow-700">
                                    Pending
                                </span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Tidak ada user pending</p>
                        @endforelse
                    </div>
                </div>

                <!-- USER APPROVED -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        ✅ User Approved ({{ $approvedUsers->count() }})
                    </h3>

                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @forelse ($approvedUsers as $user)
                            <div class="flex justify-between items-center border rounded-lg p-3 hover:bg-gray-50">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                </div>
                                <span class="text-xs px-3 py-1 rounded-full bg-green-100 text-green-700">
                                    Approved
                                </span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Belum ada user approved</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
