@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-blue-900 dark:text-blue-400">Safety Metrics Dashboard</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Monitor monthly safety performance (SR, FR, IR)</p>
    </div>

    <!-- Filters Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-8 border border-blue-700 dark:border-blue-600 shadow-lg shadow-blue-400/200">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Company Filter -->
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company</label>
                <select name="company_id"onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ $company->id == $companyId ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Year Filter -->
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Year</label>
                <select name="year"onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                    @for($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </form>
        <form method="GET" class="mb-6">
            <input type="hidden" name="year" value="{{ $year }}">
            <input type="hidden" name="company_id" value="{{ $companyId }}">

            <select name="gauge_month" onchange="this.form.submit()" class="w-full md:w-auto px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100">
                <option value="" {{ empty($gaugeMonth) ? 'selected' : '' }}>All Month</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $m == $gaugeMonth ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                    </option>
                @endfor
            </select>
        </form>

        <!-- Export Buttons -->
        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:gap-2">
            <a href="{{ route('dashboard.safety.export', ['company_id' => $companyId, 'year' => $year]) }}" 
                 class="flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                Export CSV
            </a>
            <a href="{{ route('dashboard.safety.export-pdf', ['company_id' => $companyId, 'year' => $year]) }}" 
                 class="flex items-center justify-center gap-2 px-4 py-3 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                Export PDF
            </a>
            <a href="#form" 
                 class="flex items-center justify-center gap-2 px-4 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                Tambah Data
            </a>
        </div>
    </div>

    <!-- Summary Statistics Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-8 border border-blue-700 dark:border-blue-600 shadow-lg shadow-blue-400/200">
        <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-400 mb-4">
            @if($gaugeMonth)
                Summary Data - {{ DateTime::createFromFormat('!m', $gaugeMonth)->format('F') }} {{ $year }}
            @else
                Summary Data - {{ $year }}
            @endif
        </h3>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <!-- Man Hours Card -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 text-center hover:shadow-md transition dark:bg-gray-700">
                <div class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-2">Man Hours</div>
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($monthlySummary['man_hours']) }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">hours</div>
            </div>

            <!-- Total Employees Card -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 text-center hover:shadow-md transition dark:bg-gray-700">
                <div class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-2">Total Employees</div>
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($monthlySummary['employee']) }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">people</div>
            </div>

            <!-- LTA Card -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 text-center hover:shadow-md transition dark:bg-gray-700">
                <div class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-2">Lost Time Accidents</div>
                <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $monthlySummary['lta'] }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">accidents</div>
            </div>

            <!-- Lost Work Days Card -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 text-center hover:shadow-md transition dark:bg-gray-700">
                <div class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-2">Lost Work Days</div>
                <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $monthlySummary['lost_work_days'] }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">days</div>
            </div>

            <!-- Lost Time Hours Card -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 text-center hover:shadow-md transition dark:bg-gray-700">
                <div class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-2">Lost Time Hours</div>
                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($monthlySummary['lost_time']) }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">hours</div>
            </div>

            <!-- Work Accidents Card -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 text-center hover:shadow-md transition dark:bg-gray-700">
                <div class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-2">Work Accidents</div>
                <div class="text-2xl font-bold text-red-700 dark:text-red-400">{{ $monthlySummary['kecelakaan_kerja'] }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">incidents</div>
            </div>
        </div>
    </div>

    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-400 mb-4">Severity Rate</h3>
    <!-- SR Gauge Charts -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($gaugeSR as $month => $sr)
            <div class="bg-white dark:bg-gray-800 text-center rounded-lg p-6 mb-8 border border-blue-700 dark:border-blue-600 shadow-lg shadow-blue-400/200" id="gaugeSRContainer{{ $month }}">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="font-semibold dark:text-gray-300">
                        {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                    </h4>
                    <button onclick="downloadChart('gaugeSRContainer{{ $month }}', 'SR-{{ DateTime::createFromFormat('!m', $month)->format('F') }}')" class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition">
                        Download
                    </button>
                </div>

                <canvas
                    id="gaugeSR{{ $month }}"
                    width="260"
                    height="150"
                    style="display:block;margin:auto;">
                </canvas>

                <div class="mt-2 font-bold dark:text-gray-200">{{ $sr }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Severity Rate</div>
            </div>
        @endforeach
    </div>
    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-400 mb-4">Frequency Rate</h3>
    <!-- FR Gauge Charts -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($gaugeFR as $month => $fr)
            <div class="bg-white dark:bg-gray-800 text-center rounded-lg p-6 mb-8 border border-blue-700 dark:border-blue-600 shadow-lg shadow-blue-400/200" id="gaugeFRContainer{{ $month }}">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="font-semibold dark:text-gray-300">
                        {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                    </h4>
                    <button onclick="downloadChart('gaugeFRContainer{{ $month }}', 'FR-{{ DateTime::createFromFormat('!m', $month)->format('F') }}')" class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition">
                        Download
                    </button>
                </div>

                <canvas
                    id="gaugeFR{{ $month }}"
                    width="260"
                    height="150"
                    style="display:block;margin:auto;">
                </canvas>

                <div class="mt-2 font-bold dark:text-gray-200">{{ $fr }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Frequency Rate</div>
            </div>
        @endforeach
    </div>
    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-400 mb-4">Incident Rate</h3>

    <!-- IR Gauge Charts -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($gaugeIR as $month => $ir)
            <div class="bg-white dark:bg-gray-800 text-center rounded-lg p-6 border border-blue-700 dark:border-blue-600 shadow-lg shadow-blue-400/200"
                id="gaugeIRContainer{{ $month }}">

                <div class="flex justify-between items-center mb-2">
                    <h4 class="font-semibold dark:text-gray-300">
                        {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                    </h4>
                    <button
                        onclick="downloadChart('gaugeIRContainer{{ $month }}', 'IR-{{ DateTime::createFromFormat('!m', $month)->format('F') }}')"
                        class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition">
                        Download
                    </button>
                </div>

                <canvas
                    id="gaugeIR{{ $month }}"
                    width="260"
                    height="150"
                    style="display:block;margin:auto;">
                </canvas>

                <div class="mt-2 font-bold dark:text-gray-200">{{ $ir }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Incident Rate</div>
            </div>
        @endforeach
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- SR Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-8 border border-blue-700 dark:border-blue-600 shadow-lg shadow-blue-400/200" id="srChartContainer">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-300">Severity Rate (SR)</h3>
                <button onclick="downloadChart('srChartContainer', 'Severity-Rate')" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                    Download
                </button>
            </div>
            <div class="relative h-80">
                <canvas id="srChart"></canvas>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">Formula: SR = (Lost Time × 1,000,000) / Total Man Hours</p>
        </div>

        <!-- FR Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-8 border border-blue-700 dark:border-blue-600 shadow-lg shadow-blue-400/200" id="frChartContainer">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-300">Frequency Rate (FR)</h3>
                <button onclick="downloadChart('frChartContainer', 'Frequency-Rate')" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                    Download
                </button>
            </div>
            <div class="relative h-80">
                <canvas id="frChart"></canvas>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">Formula: FR = (Lost Work Days × 1,000,000) / Total Man Hours</p>
        </div>

        <!-- IR Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-8 border border-blue-700 dark:border-blue-600 shadow-lg shadow-blue-400/200" id="irChartContainer">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-300">Incident Rate (IR)</h3>
                <button onclick="downloadChart('irChartContainer', 'Incident-Rate')" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                    Download
                </button>
            </div>
            <div class="relative h-80">
                <canvas id="irChart"></canvas>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">Formula: IR = (Total Work Accidents × 100) / Total Employees</p>
        </div>

        <!-- Comparison Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-8 border border-blue-700 dark:border-blue-600 shadow-lg shadow-blue-400/200" id="comparisonChartContainer">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-300">Monthly Comparison</h3>
                <button onclick="downloadChart('comparisonChartContainer', 'Monthly-Comparison')" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                    Download
                </button>
            </div>
            <div class="relative h-80">
                <canvas id="comparisonChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Company Statistics Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-8 border border-blue-700 dark:border-blue-600 shadow-lg shadow-blue-400/200">
        <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-400 mb-4">Company Statistics Data</h3>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] table table-bordered table-sm dark:bg-gray-700">
                <thead style="background:#3b82f6;color:white;">
                    <tr>
                        <th class="text-center">Company</th>
                        <th class="text-center">Period</th>
                        <th class="text-center">Man Hours</th>
                        <th class="text-center">Employees</th>
                        <th class="text-center">LTA</th>
                        <th class="text-center">Lost Work Days</th>
                        <th class="text-center">Lost Time</th>
                        <th class="text-center">Work Accidents</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="dark:border-gray-600">
                    @forelse($statistics as $stat)
                        <tr class="dark:border-gray-600 hover:bg-blue-50 dark:hover:bg-gray-700 transition">
                            <td class="dark:text-gray-300">{{ $stat->company->name ?? 'N/A' }}</td>
                            <td class="text-center dark:text-gray-300">
                                {{ \Carbon\Carbon::create()->month($stat->period->month)->format('M') }} {{ $stat->period->year }}
                            </td>
                            <td class="text-center dark:text-gray-300">{{ number_format($stat->man_hours) }}</td>
                            <td class="text-center dark:text-gray-300">{{ number_format($stat->employee) }}</td>
                            <td class="text-center dark:text-gray-300">{{ $stat->lta }}</td>
                            <td class="text-center dark:text-gray-300">{{ $stat->lost_work_days }}</td>
                            <td class="text-center dark:text-gray-300">{{ number_format($stat->lost_time) }}</td>
                            <td class="text-center dark:text-gray-300">{{ $stat->kecelakaan_kerja }}</td>
                            <td class="text-center">
                                <form action="{{ route('statistics.company.destroy', $stat->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded transition" onclick="return confirm('Are you sure you want to delete this record?');">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr class="dark:border-gray-600">
                            <td colspan="9" class="text-center text-gray-500 dark:text-gray-400 py-4">No data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $statistics->links() }}
        </div>
    </div>

    <!-- Input Form Section -->
    <div id="form" class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-8 border border-blue-700 dark:border-blue-600 shadow-lg shadow-blue-400/200">
        {{-- ALERT SUCCESS --}}
        @if(session('success'))
            <p style="color:green">{{ session('success') }}</p>
        @endif

        {{-- ALERT ERROR --}}
        @if($errors->any())
            <ul style="color:red">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-200 mb-6">Add Monthly Data</h2>
        
        <form action="{{ route('dashboard.safety.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Company Select -->
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

                <!-- Month Select -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Month *</label>
                    <select name="month" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">-- Select Month --</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ old('month') == $m ? 'selected' : '' }}>
                                {{ \DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endfor
                    </select>
                    @error('month')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Year Select -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Year *</label>
                    <select name="year" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">-- Select Year --</option>
                        @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ old('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    @error('year')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Man Hours -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Man Hours *</label>
                    <input type="number" name="man_hours" value="{{ old('man_hours') }}" required min="0" 
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                    @error('man_hours')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Employee -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Total Employees *</label>
                    <input type="number" name="employee" value="{{ old('employee') }}" required min="0" 
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                    @error('employee')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- LTA -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">LTA (Lost Time Accidents) *</label>
                    <input type="number" name="lta" value="{{ old('lta') }}" required min="0" 
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                    @error('lta')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lost Work Days -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lost Work Days *</label>
                    <input type="number" name="lost_work_days" value="{{ old('lost_work_days') }}" required min="0" 
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                    @error('lost_work_days')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lost Time (hours) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lost Time *</label>
                    <input type="number" name="lost_time" value="{{ old('lost_time') }}" required min="0" 
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                    @error('lost_time')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Work Accidents -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Work Accidents *</label>
                    <input type="number" name="kecelakaan_kerja" value="{{ old('kecelakaan_kerja') }}" required min="0" 
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                    @error('kecelakaan_kerja')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                Save Safety Data
            </button>
        </form>
    </div>
</div>

@push('scripts')
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

    document.addEventListener('DOMContentLoaded', function() {
        const chartData = @json($chartData);
        const months = chartData.labels;
        const frData = @json($monthlyFR);
        const gaugeData = @json($gaugeFR);
        const gaugeSRData = @json($gaugeSR);
        const gaugeIRData = @json($gaugeIR);

        const needlePlugin = {
            id: 'needle',
            afterDatasetDraw(chart, args, opts) {
                const { ctx } = chart;
                const meta = chart.getDatasetMeta(0).data[0];

                if (!meta) return;

                const value = opts.value;
                const max = opts.max;

                const angle = Math.PI + (value / max) * Math.PI;

                ctx.save();
                ctx.translate(meta.x, meta.y);
                ctx.rotate(angle);

                ctx.beginPath();
                ctx.moveTo(0, -2);
                ctx.lineTo(meta.outerRadius - 10, 0);
                ctx.lineTo(0, 2);
                ctx.fillStyle = '#111';
                ctx.fill();

                ctx.restore();

                // center dot
                ctx.beginPath();
                ctx.arc(meta.x, meta.y, 4, 0, Math.PI * 2);
                ctx.fill();
            }
        };

        Chart.register(needlePlugin);

        Object.entries(gaugeSRData).forEach(([month, value]) => {
            const canvas = document.getElementById('gaugeSR' + month);
            if (!canvas) return;

            new Chart(canvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [40, 30, 30],
                        backgroundColor: ['#2ecc71', '#f1c40f', '#e74c3c'],
                        borderWidth: 0,
                        circumference: 180,
                        rotation: 270,
                    }]
                },
                options: {
                    responsive: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false },
                        needle: {
                            value: value,
                            max: 100
                        }
                    }
                }
            });
        });

        Object.entries(gaugeData).forEach(([month, value]) => {
            const canvas = document.getElementById('gaugeFR' + month);
            if (!canvas) return;

            new Chart(canvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [40, 30, 30],
                        backgroundColor: ['#2ecc71', '#f1c40f', '#e74c3c'],
                        borderWidth: 0,
                        circumference: 180,
                        rotation: 270,
                    }]
                },
                options: {
                    responsive: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false },
                        needle: {
                            value: value,
                            max: 100
                        }
                    }
                }
            });
        });

        Object.entries(gaugeIRData).forEach(([month, value]) => {
            const canvas = document.getElementById('gaugeIR' + month);
            if (!canvas) return;

            new Chart(canvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [40, 30, 30],
                        backgroundColor: ['#2ecc71', '#f1c40f', '#e74c3c'],
                        borderWidth: 0,
                        circumference: 180,
                        rotation: 270,
                    }]
                },
                options: {
                    responsive: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false },
                        needle: {
                            value: value,
                            max: 100
                        }
                    }
                }
            });
        });

        const srChartElement = document.getElementById('srChart');
        if (srChartElement) {
            new Chart(srChartElement, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Severity Rate',
                        data: chartData.sr,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: true,
                    plugins: { legend: { display: true } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        const frChartElement = document.getElementById('frChart');
        if (frChartElement) {
            new Chart(frChartElement, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Frequency Rate',
                        data: chartData.fr,
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: true,
                    plugins: { legend: { display: true } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        const irChartElement = document.getElementById('irChart');
        if (irChartElement) {
            new Chart(irChartElement, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Incident Rate',
                        data: chartData.ir,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: true,
                    plugins: { legend: { display: true } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        const comparisonChartElement = document.getElementById('comparisonChart');
        if (comparisonChartElement) {
            new Chart(comparisonChartElement, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'SR',
                            data: chartData.sr,
                            backgroundColor: '#ef4444'
                        },
                        {
                            label: 'FR',
                            data: chartData.fr,
                            backgroundColor: '#f97316'
                        },
                        {
                            label: 'IR',
                            data: chartData.ir,
                            backgroundColor: '#3b82f6'
                        }
                    ]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: true,
                    plugins: { legend: { display: true } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }
    });
</script>
@endpush
@endsection
