@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-6">

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
        <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">
            Last Activity
        </h2>

        <ul class="space-y-3 text-sm">
            @foreach ($activities as $activity)
            <li class="flex justify-between items-center">

                {{-- USER --}}
                <div>
                    <div class="font-medium text-gray-700">
                        {{ $activity->user->name ?? 'Unknown User' }}
                    </div>
                    <div class="text-xs text-gray-400">
                        Last login {{ optional($activity->user?->last_login_at)->diffForHumans() ?? '-' }}
                    </div>
                </div>

                {{-- ACTIVITY --}}
                <div class="text-gray-700">
                    {{ $activity->description }}
                </div>

                {{-- TIME --}}
                <div class="text-xs text-gray-400">
                    {{ $activity->created_at->diffForHumans() }}
                </div>

            </li>
            @endforeach
        </ul>
    </div>

</div>
@endsection
