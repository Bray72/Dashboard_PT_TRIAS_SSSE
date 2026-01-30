@extends('layouts.app')

@section('content')
        <div class="container mx-auto px-4 py-8">
            <div class="card-body">
                <div class="mb-8">
                    <h1 class="text-4xl font-bold text-blue-900 dark:text-blue-400">Work Permit Dashboard</h1>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-8 border border-green-600 dark:border-green-600 shadow-lg shadow-green-400/200">
                    <form method="GET" action="{{ route('dashboard.work-permit') }}">
                        <div class="row g-3 align-items-end">

                            {{-- Bulan --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold dark:text-gray-300">Bulan</label>
                                <select name="month" class="form-select dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            {{-- Tahun --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold dark:text-gray-300">Tahun</label>
                                <select name="year" class="form-select dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600">
                                    @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label fw-bold dark:text-gray-300">Company</label>
                                <select name="company_id" class="form-select dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600">
                                    <option value="">Semua Company</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}"
                                            {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div> <br>

                            {{-- Button --}}
                            <div class="col-md-6">
                                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition">
                                    Tampilkan
                                </button>
                            </div>

                        </div>
                    </form>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('dashboard.safety.export', ['company_id' => $companyId, 'year' => $year]) }}" 
                            class="w-full justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200 inline-flex items-center gap-2">
                            Export CSV
                        </a>
                        <a href="{{ route('dashboard.safety.export-pdf', ['company_id' => $companyId, 'year' => $year]) }}" 
                            class="w-full justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition duration-200 inline-flex items-center gap-2">
                            Export PDF
                        </a>
                        <a href="#form" 
                            class="w-full justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition duration-200 inline-flex items-center gap-2">
                            Tambah Data
                        </a>
                    </div>
                </div>
                
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-8 border border-green-600 dark:border-green-600 shadow-lg shadow-green-400/200">
                <div class="overflow-x-auto">
                    <table class="table table-bordered table-sm align-middle w-full min-w-[600px]">
                        <thead style="background:#9bbb59;color:white;">
                            <tr>
                                <th class="text-start">Safety Work Permit</th>
                                <th class="text-center">
                                    {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('M') }}'{{ substr($year,2) }}
                                </th>
                                <th class="text-center">YTD'{{ substr($year,2) }}</th>
                                <th class="text-center">YTD'{{ substr($year-1,2) }}</th>
                            </tr>
                        </thead>
                        <tbody class="dark:bg-gray-700">
                            @foreach ($permitTypes as $type)
                            <tr class="dark:border-gray-600">
                                <td class="fw-bold dark:text-gray-300">{{ $type->name }}</td>

                                <td class="text-center fw-bold dark:text-gray-300">
                                    {{ $monthly[$type->id]->total ?? 0 }}
                                </td>

                                <td class="text-center fw-bold dark:text-gray-300">
                                    {{ $ytdCurrent[$type->id]->total ?? 0 }}
                                </td>

                                <td class="text-center fw-bold text-success dark:text-green-400">
                                    {{ $ytdPrevious[$type->id]->total ?? 0 }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-8 border border-green-600 dark:border-green-600 shadow-lg shadow-green-400/200" id="permitTrendsContainer">
            <div class="card-shadow mb-4">
                <div class="card dark:bg-gray-800">
                    <div class="card-body">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-green-900 dark:text-green-400">Permit Trends - Year {{ $year }}</h3>
                            <button onclick="downloadChart('permitTrendsContainer', 'Permit-Trends')" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition">
                                Download
                            </button>
                        </div>
                        <canvas id="permitTrendsChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div id="form" class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-8 border border-green-600 dark:border-green-600 shadow-lg shadow-green-400/200">
            <form method="POST" action="{{ route('dashboard.work-permit.store') }}">
                <h2 class="text-2xl font-bold text-green-900 dark:text-green-400 mb-6">Input Work Permit</h2>
                @csrf

                <div class="grid grid-cols-3 gap-4 mb-4">
                    <select name="company_id" required class="dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600 border rounded px-2 py-1">
                        <option value="">-- Pilih Company --</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>

                    <select name="month" required class="dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600 border rounded px-2 py-1">
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ $m }}">{{ DateTime::createFromFormat('!m',$m)->format('F') }}</option>
                        @endfor
                    </select>

                    <input type="number" name="year" value="{{ date('Y') }}" required class="dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600 border rounded px-2 py-1">
                </div>

                <table class="w-full mb-4 table table-bordered table-sm dark:bg-gray-700">
                    @foreach($permitTypes as $permit)
                    <tr class="dark:border-gray-600">
                        <td class="dark:text-gray-300">{{ $permit->name }}</td>
                        <td>
                            <input
                                type="number"
                                name="permits[{{ $permit->id }}]"
                                value="0"
                                min="0"
                                class="border p-1 w-24 dark:bg-gray-600 dark:text-gray-100 dark:border-gray-500"
                            >
                        </td>
                    </tr>
                    @endforeach
                </table>

                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition">
                    Simpan
                </button>
            </form>
        </div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
