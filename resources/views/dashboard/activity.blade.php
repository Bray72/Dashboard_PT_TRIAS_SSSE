@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-6">

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
        <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">
            Last Activity
        </h2>

        <div class="text-sm text-gray-700">
            Last login:
            <span class="font-medium">
                {{ optional(auth()->user()->last_login_at)->diffForHumans() ?? '-' }}
            </span>
        </div>

        <ul class="space-y-3 text-sm">
            @forelse ($activities as $activity)
                <li class="flex justify-between items-center">
                    <span class="text-gray-700">
                        {{ $activity->activity }}
                    </span>

                    <span class="text-gray-400">
                        {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}
                    </span>
                </li>
            @empty
                <li class="text-gray-400">Belum ada aktivitas</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
