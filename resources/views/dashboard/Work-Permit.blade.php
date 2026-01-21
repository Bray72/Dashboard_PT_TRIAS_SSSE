@extends('layouts.app')

@section('content')
        <div class="container mx-auto px-4 py-8">
            <div class="card-body">
                <div class="mb-8">
                    <h1 class="text-4xl font-bold text-blue-900">Work Permit Dashboard</h1>
                </div>
                <div class="bg-white rounded-lg p-6 mb-8 border border-green-600 shadow-lg shadow-green-400/200">
                    <form method="GET" action="{{ route('dashboard.work-permit') }}">
                        <div class="row g-3 align-items-end">

                            {{-- Bulan --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Bulan</label>
                                <select name="month" class="form-select">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            {{-- Tahun --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tahun</label>
                                <select name="year" class="form-select">
                                    @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div> <br>

                            {{-- Button --}}
                            <div class="col-md-6">
                                <button class="bg-green-600 text-white px-4 py-2 rounded">
                                    Tampilkan
                                </button>
                            </div>

                        </div>
                    </form>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('dashboard.work-permit.export', ['month' => $month, 'year' => $year]) }}" 
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition duration-200 inline-flex items-center gap-2">
                            Export CSV
                        </a>
                        <a href="{{ route('dashboard.work-permit.export-pdf', ['month' => $month, 'year' => $year]) }}" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition duration-200 inline-flex items-center gap-2">
                            Export PDF
                        </a>
                    </div>
                </div>
                
            </div>
            <div class="bg-white rounded-lg p-6 mb-8 border border-green-600 shadow-lg shadow-green-400/200">
                <div class="overflow-x-auto">
                    <table class="table table-bordered table-sm align-middle w-full min-w-[600px]">
                        <thead style="background:#9bbb59;color:white;">
                            <tr>
                                <th class="text-start">Safety Work Permit</th>
                                <th class="text-center">
                                    {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('M') }}’{{ substr($year,2) }}
                                </th>
                                <th class="text-center">YTD’{{ substr($year,2) }}</th>
                                <th class="text-center">YTD’{{ substr($year-1,2) }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permitTypes as $type)
                            <tr>
                                <td class="fw-bold">{{ $type->name }}</td>

                                <td class="text-center fw-bold">
                                    {{ $monthly[$type->id]->total ?? 0 }}
                                </td>

                                <td class="text-center fw-bold">
                                    {{ $ytdCurrent[$type->id]->total ?? 0 }}
                                </td>

                                <td class="text-center fw-bold text-success">
                                    {{ $ytdPrevious[$type->id]->total ?? 0 }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        <div class="bg-white rounded-lg p-6 mb-8 border border-green-600 shadow-lg shadow-green-400/200">
            <div class="card-shadow mb-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-xl font-bold text-green-900 mb-4">Permit Trends - Year {{ $year }}</h3>
                        <canvas id="permitTrendsChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>    
        <div class="bg-white rounded-lg p-6 mb-8 border border-green-600 shadow-lg shadow-green-400/200">
            <form method="POST" action="{{ route('dashboard.work-permit.store') }}">
                <h2 class="text-2xl font-bold text-green-900 mb-6">Input Work Permit</h2>
                @csrf

                <input type="hidden" name="company_id" value="1">

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <select name="month" required>
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ $m }}">{{ DateTime::createFromFormat('!m',$m)->format('F') }}</option>
                        @endfor
                    </select>

                    <input type="number" name="year" value="{{ date('Y') }}" required>
                </div>

                <table class="w-full mb-4 table table-bordered table-sm">
                    @foreach($permitTypes as $permit)
                    <tr>
                        <td>{{ $permit->name }}</td>
                        <td>
                            <input
                                type="number"
                                name="permits[{{ $permit->id }}]"
                                value="0"
                                min="0"
                                class="border p-1 w-24"
                            >
                        </td>
                    </tr>
                    @endforeach
                </table>

                <button class="bg-green-600 text-white px-4 py-2 rounded">
                    Simpan
                </button>
            </form>
        </div>    
    </div>
</div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const trendsCtx = document.getElementById('permitTrendsChart');
    if (trendsCtx) {
        const chartDataFromServer = {!! json_encode($chartData) !!};
        
        const colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
        ];

        const datasets = {!! json_encode(
            $permitTypes->map(function($type, $index) use ($chartData) {
                return [
                    'label' => $type->name,
                    'data' => array_values($chartData[$type->id] ?? array_fill(1, 12, 0)),
                    'borderColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'][$index % 6],
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'][$index % 6],
                    'borderWidth' => 2,
                    'tension' => 0.4,
                    'fill' => false
                ];
            })->values()
        ) !!};

        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Permits'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    }
                }
            }
        });
    }

    const ctx = document.getElementById('ytdChart');
    if (!ctx) {
        console.log('Canvas ytdChart tidak ditemukan');
        return;
    }

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($permitTypes->pluck('name')->values()) !!},
            datasets: [{
                label: 'YTD {{ $year }}',
                data: {!! json_encode(
                    $permitTypes->map(fn($t) => $ytdCurrent[$t->id]->total ?? 0)->values()
                ) !!},
                backgroundColor: '#4CAF50'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

});
</script>
