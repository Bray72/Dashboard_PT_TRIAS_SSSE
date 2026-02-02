@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-6">

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
        <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">
            Last Activity
        </h2>

        <ul class="space-y-3 text-sm">

            @forelse ($activities as $activity)
                <li class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">
                       {{ optional(auth()->user()->last_login_at)->diffForHumans() ?? '-' }}
                    </span>

                    <span class="text-gray-700 dark:text-gray-300">
                        {{ $activity['message'] }}
                    </span>

                    <span class="text-xs text-gray-400">
                        {{ $activity['time']->diffForHumans() }}
                    </span>
                </li>
            @empty
                <li class="text-gray-500 text-sm">
                    Belum ada aktivitas
                </li>
            @endforelse
        </ul>
    </div>

</div>
@endsection
