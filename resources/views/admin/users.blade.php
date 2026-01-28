@extends('layouts.app')

@section('content')
<div class="transition-colors duration-300">

    <!-- HEADER -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-blue-900 dark:text-blue-400">
            Detail User
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            Daftar Semua User
        </p>
    </div>

    <!-- TABLE USER PENDING -->
    <div class="bg-white dark:bg-gray-900
                border border-gray-200 dark:border-gray-700
                rounded-xl shadow dark:shadow-none
                p-6 mb-10">

        <table class="w-full">
            <thead class="border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th class="text-left py-3 text-gray-700 dark:text-gray-300">NAMA</th>
                    <th class="text-left py-3 text-gray-700 dark:text-gray-300">EMAIL</th>
                    <th class="text-left py-3 text-gray-700 dark:text-gray-300">STATUS</th>
                    <th class="text-left py-3 text-gray-700 dark:text-gray-300">AKSI</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td colspan="4" class="py-6 text-center text-gray-600 dark:text-gray-400">
                        Tidak ada user pending
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- CARD USER -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- USER PENDING -->
        <div class="bg-white dark:bg-gray-900
                    border border-gray-200 dark:border-gray-700
                    rounded-xl p-6">

            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                User Pending (0)
            </h3>

            <p class="text-gray-500 dark:text-gray-400">
                Kosong
            </p>
        </div>

        <!-- USER APPROVED -->
        <div class="bg-white dark:bg-gray-900
                    border border-gray-200 dark:border-gray-700
                    rounded-xl p-6">

            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                User Approved (4)
            </h3>

            <div class="space-y-3 max-h-64 overflow-y-auto pr-2">

                <div class="bg-gray-50 dark:bg-gray-800
                            border border-gray-200 dark:border-gray-700
                            rounded-lg p-4 flex justify-between items-center">

                    <div>
                        <p class="font-medium text-gray-800 dark:text-gray-200">
                            adhi
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            adhi@gmail.com
                        </p>
                    </div>

                    <span class="px-3 py-1 rounded-full text-sm
                                 bg-green-100 dark:bg-green-900
                                 text-green-700 dark:text-green-300">
                        Approved
                    </span>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
