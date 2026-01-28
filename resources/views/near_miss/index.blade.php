@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-blue-900 dark:text-blue-400">Near Miss Dashboard</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Monitor Near Miss incidents and safety trends</p>
    </div>

    <!-- Filters Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
        <form method="GET" class="flex gap-4 items-end flex-wrap">
            <!-- Year Filter -->
            <div class="flex-1 min-w-48">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Year</label>
                <select name="year" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                    @for($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Month Filter -->
            <div class="flex-1 min-w-48">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Month</label>
                <select name="month" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                    <option value="">All Months</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- Company Filter -->
            <div class="flex-1 min-w-48">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company</label>
                <select name="company_id" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                    <option value="">All Companies</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ ($companyId ?? null) == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- KPI Cards Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Near Miss Card -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-lg shadow p-6 border-l-4 border-blue-600">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-1">Total Near Miss</p>
                    <h3 class="text-4xl font-bold text-blue-900 dark:text-blue-400">{{ $totalNearMiss }}</h3>
                </div>
                <div class="bg-blue-600 text-white rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-600 dark:text-gray-400">Incidents reported</p>
        </div>

        <!-- Near Miss Rate Card -->
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900 dark:to-orange-800 rounded-lg shadow p-6 border-l-4 border-orange-600">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-1">Near Miss Rate</p>
                    <h3 class="text-4xl font-bold text-orange-900 dark:text-orange-400">{{ number_format($nearMissRate, 4) }}</h3>
                </div>
                <div class="bg-orange-600 text-white rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-600 dark:text-gray-400">
                <strong>Formula:</strong> Qty Near Miss / Man Hours * 100.000
            </p>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Total Man Hours: <strong>{{ number_format($manHours) }}</strong></p>
        </div>

        <!-- Open Status Card -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-lg shadow p-6 border-l-4 border-green-600">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-1">Open Cases</p>
                    <h3 class="text-4xl font-bold text-green-900 dark:text-green-400">{{ $status['Open'] ?? 0 }}</h3>
                </div>
                <div class="bg-green-600 text-white rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-600 dark:text-gray-400">Cases in progress</p>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Risk Level Pie Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" id="riskChartContainer">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-300">Risk Level Distribution</h3>
                <button onclick="downloadChart('riskChartContainer', 'Risk-Level-Distribution')" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                    Download
                </button>
            </div>
            <div class="relative h-80">
                <canvas id="riskChart"></canvas>
            </div>
        </div>

        <!-- Severity Pie Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" id="severityChartContainer">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-300">Severity Distribution</h3>
                <button onclick="downloadChart('severityChartContainer', 'Severity-Distribution')" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                    Download
                </button>
            </div>
            <div class="relative h-80">
                <canvas id="severityChart"></canvas>
            </div>
        </div>

        <!-- Category Bar Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" id="categoryChartContainer">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-300">Near Miss by Category</h3>
                <button onclick="downloadChart('categoryChartContainer', 'Near-Miss-by-Category')" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                    Download
                </button>
            </div>
            <div class="relative h-80">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        <!-- Department Bar Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" id="departmentChartContainer">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-300">Near Miss by Department</h3>
                <button onclick="downloadChart('departmentChartContainer', 'Near-Miss-by-Department')" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                    Download
                </button>
            </div>
            <div class="relative h-80">
                <canvas id="departmentChart"></canvas>
            </div>
        </div>

        <!-- Likelihood Pie Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" id="likelihoodChartContainer">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-300">Likelihood Distribution</h3>
                <button onclick="downloadChart('likelihoodChartContainer', 'Likelihood-Distribution')" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                    Download
                </button>
            </div>
            <div class="relative h-80">
                <canvas id="likelihoodChart"></canvas>
            </div>
        </div>

        <!-- Monthly Trend Line Chart -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md" id="companyChartContainer">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold dark:text-gray-300">Near Miss per Company</h2>
                <button onclick="downloadChart('companyChartContainer', 'Near-Miss-per-Company')" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                    Download
                </button>
            </div>
            <canvas id="nearMissCompanyChart"></canvas>
        </div>
    </div>

    <!-- Near Miss Data Table Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-200">Near Miss Reports</h2>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">List of all near miss incidents</p>
        </div>

        @if($nearMisses->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Risk Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($nearMisses as $nearMiss)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">{{ $nearMiss->date->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">{{ $nearMiss->location }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">{{ $nearMiss->department->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">{{ $nearMiss->category }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @php
                                        $riskColors = [
                                            'Low' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                            'Medium' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                            'High' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $riskColors[$nearMiss->risk_level] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                        {{ $nearMiss->risk_level }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @php
                                        $statusColors = [
                                            'Open' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                            'Closed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$nearMiss->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                        {{ $nearMiss->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <button type="button" 
                                        onclick="openEditModal({{ $nearMiss->id }}, '{{ $nearMiss->status }}')"
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 font-medium text-sm">
                                        Edit Status
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex justify-center">
                {{ $nearMisses->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 00-.707.293h-3.172a1 1 0 00-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-300">No near miss reports</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start by adding a near miss report using the form above.</p>
            </div>
        @endif
    </div>

    <!-- Status Distribution Cards -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200 mb-6">Status Overview</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($status as $statusName => $count)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 flex justify-between items-center dark:bg-gray-700">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 font-medium">{{ $statusName }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Cases</p>
                    </div>
                    <div class="text-3xl font-bold {{ $statusName === 'Open' ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400' }}">
                        {{ $count }}
                    </div>
                </div>
            @empty
                <p class="text-gray-500 dark:text-gray-400">No status data available</p>
            @endforelse
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-200 mb-6">Add New Near Miss Report</h2>

        @if(session('success'))
            <div class="bg-green-100 dark:bg-green-900 border-l-4 border-green-500 text-green-700 dark:text-green-200 p-4 mb-6">
                <p class="font-bold">Success!</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 dark:bg-red-900 border-l-4 border-red-500 text-red-700 dark:text-red-200 p-4 mb-6">
                <p class="font-bold">Error!</p>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('near-miss.store') }}" class="space-y-6">
            @csrf
            <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company *</label>
                    <select name="company_id" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">-- Select Company --</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                    @error('company_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date *</label>
                    <input type="date" name="date" value="{{ old('date') }}" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                    @error('date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Department -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Department *</label>
                    <select name="department_id" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">-- Select Department --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Location *</label>
                    <input type="text" name="location" value="{{ old('location') }}" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                    @error('location')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category *</label>
                    <select name="category" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">-- Select Category --</option>
                        <option value="Unsafe Act" {{ old('category') == 'Unsafe Act' ? 'selected' : '' }}>Unsafe Act</option>
                        <option value="Unsafe Condition" {{ old('category') == 'Unsafe Condition' ? 'selected' : '' }}>Unsafe Condition</option>
                    </select>
                    @error('category')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Severity -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Severity *</label>
                    <select name="severity" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">-- Select Severity --</option>
                        <option value="Low" {{ old('severity') == 'Low' ? 'selected' : '' }}>Low</option>
                        <option value="Medium" {{ old('severity') == 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="High" {{ old('severity') == 'High' ? 'selected' : '' }}>High</option>
                    </select>
                    @error('severity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Likelihood -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Likelihood *</label>
                    <select name="likelihood" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">-- Select Likelihood --</option>
                        <option value="Low" {{ old('likelihood') == 'Low' ? 'selected' : '' }}>Low</option>
                        <option value="Medium" {{ old('likelihood') == 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="High" {{ old('likelihood') == 'High' ? 'selected' : '' }}>High</option>
                    </select>
                    @error('likelihood')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description *</label>
                    <textarea name="description" rows="4" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Required -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Action Required</label>
                    <textarea name="action_required" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">{{ old('action_required') }}</textarea>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                Submit Near Miss Report
            </button>
        </form>
    </div>

    <!-- Edit Status Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">Update Near Miss Status</h3>
            </div>
            
            <form id="editStatusForm" method="POST" class="p-6">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" id="statusSelect" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100">
                        <option value="Open">Open</option>
                        <option value="Closed">Closed</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" 
                        onclick="closeEditModal()"
                        class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    // Fungsi untuk download chart sebagai gambar
    async function downloadChart(containerId, filename) {
        try {
            const element = document.getElementById(containerId);
            if (!element) {
                alert('Chart tidak ditemukan!');
                return;
            }

            // Tampilkan loading state
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '';
            button.disabled = true;

            // Gunakan html2canvas untuk capture element
            const canvas = await html2canvas(element, {
                scale: 2,
                backgroundColor: '#ffffff',
                allowTaint: true,
                useCORS: true,
                logging: false
            });

            // Convert ke blob dan download
            canvas.toBlob(function(blob) {
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = filename + '_' + new Date().toISOString().split('T')[0] + '.png';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);

                // Restore button state
                button.innerHTML = originalText;
                button.disabled = false;
            });
        } catch (error) {
            console.error('Error downloading chart:', error);
            alert('Gagal mendownload chart. Silakan coba lagi.');
            button.innerHTML = originalText;
            button.disabled = false;
        }
    }

    const companyLabels = {!! json_encode($nearMissPerCompany->pluck('company_name')) !!};
    const companyTotals = {!! json_encode($nearMissPerCompany->pluck('total')) !!};

    const ctxCompany = document.getElementById('nearMissCompanyChart');

    new Chart(ctxCompany, {
        type: 'bar',
        data: {
            labels: companyLabels,
            datasets: [{
                label: 'Total Near Miss',
                data: companyTotals,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function openEditModal(id, currentStatus) {
        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('statusSelect').value = currentStatus;
        document.getElementById('editStatusForm').action = `/near-miss/${id}/status`;
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const chartColors = {
            low: '#2ecc71',
            medium: '#f1c40f',
            high: '#e74c3c'
        };

        // Risk Level Chart
        const riskData = @json($risk);
        new Chart(document.getElementById('riskChart'), {
            type: 'doughnut',
            data: {
                labels: ['High', 'Medium', 'Low'],
                datasets: [{
                    data: [
                        riskData['High'] || 0,
                        riskData['Medium'] || 0,
                        riskData['Low'] || 0
                    ],
                    backgroundColor: ['#e74c3c', '#f1c40f', '#2ecc71'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Severity Chart
        const severityData = @json($severity);
        new Chart(document.getElementById('severityChart'), {
            type: 'doughnut',
            data: {
                labels: ['High', 'Medium', 'Low'],
                datasets: [{
                    data: [
                        severityData['High'] || 0,
                        severityData['Medium'] || 0,
                        severityData['Low'] || 0
                    ],
                    backgroundColor: ['#e74c3c', '#f1c40f', '#2ecc71'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Category Chart
        const categoryData = @json($category);
        new Chart(document.getElementById('categoryChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(categoryData),
                datasets: [{
                    label: 'Count',
                    data: Object.values(categoryData),
                    backgroundColor: '#3b82f6',
                    borderColor: '#1e40af',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });

        // Department Chart
        const departmentData = @json($departmentStats);
        new Chart(document.getElementById('departmentChart'), {
            type: 'bar',
            data: {
                labels: departmentData.map(d => d.name),
                datasets: [{
                    label: 'Near Miss Count',
                    data: departmentData.map(d => d.total),
                    backgroundColor: '#8b5cf6',
                    borderColor: '#6d28d9',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });

        // Likelihood Chart
        const likelihoodData = @json($likelihood);
        new Chart(document.getElementById('likelihoodChart'), {
            type: 'doughnut',
            data: {
                labels: ['High', 'Medium', 'Low'],
                datasets: [{
                    data: [
                        likelihoodData['High'] || 0,
                        likelihoodData['Medium'] || 0,
                        likelihoodData['Low'] || 0
                    ],
                    backgroundColor: ['#e74c3c', '#f1c40f', '#2ecc71'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Monthly Trend Chart
        const trendData = @json($monthlyTrend);
        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: trendData.map(d => d.month),
                datasets: [{
                    label: 'Near Miss Count',
                    data: trendData.map(d => d.count),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });

        // Close modal when clicking outside
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    });
</script>
@endpush

@endsection
