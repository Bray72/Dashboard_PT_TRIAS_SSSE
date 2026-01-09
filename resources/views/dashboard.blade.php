<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Bulanan</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background: #f5f6fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .topbar {
            background: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #eee;
        }

        .nav-tabs a {
            margin-right: 20px;
            font-weight: 600;
            cursor: pointer;
            color: #333;
            text-decoration: none;
        }

        .nav-tabs a.active {
            color: #0d6efd;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 5px;
        }

        .stat-card {
            background: linear-gradient(135deg, #0b132b, #1c2541);
            color: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }

        .chart-wrapper {
            background: linear-gradient(135deg, #0b132b, #1c2541);
            border-radius: 16px;
            padding: 30px;
            margin-top: 30px;
        }

        .chart-box {
            background: white;
            border-radius: 10px;
            padding: 15px;
        }

        .form-wrapper {
            background: linear-gradient(135deg, #0b132b, #1c2541);
            border-radius: 16px;
            padding: 25px;
            margin-top: 30px;
            color: white;
        }

        .btn-save {
            background: #4ea8de;
            border: none;
            border-radius: 20px;
            padding: 8px 25px;
            color: white;
        }
    </style>
</head>
<body>

<!-- HEADER -->
<div class="topbar">
    <div>
        <img src="/ppt.png" width="300">
    </div>

    <div class="nav-tabs">
        @foreach($companies as $company)
            <a href="{{ route('dashboard', ['company_id' => $company->id]) }}"
               class="{{ $companyId == $company->id ? 'active' : '' }}">
                {{ strtoupper($company->name) }}
            </a>
        @endforeach
    </div>

    <div>
        <h5>{{ $companies->firstWhere('id', $companyId)?->name }}</h5>
    </div>
</div>



<div class="container mt-4">
    
    <div class="d-flex align-items-center">
        <h4 class="fw-bold mb-0">Dashboard</h4>

        <form class="ms-auto d-flex align-items-center">
            <select name="year" class="form-select w-auto d-inline">
                @foreach(\App\Models\Period::select('year')->distinct()->orderBy('year','desc')->pluck('year') as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endforeach
            </select> 
            <button class="btn btn-sm btn-primary">Tampilkan</button>
        </form>
    </div> <br>

    <!-- SUMMARY -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="stat-card">
                <small>Total Man Hours</small>
                <h3>{{ array_sum($chartData['man_hours']) }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <small>Employees</small>
                <h3>{{ array_sum($chartData['employee']) }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <small>LTA</small>
                <h3>{{ array_sum($chartData['lta']) }}</h3>
            </div>
        </div>
    </div>

    <!-- CHART -->
    <div class="chart-wrapper">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="chart-box">
                    <canvas id="manHourChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-box">
                    <canvas id="employeeChart"></canvas>
                </div>
            </div>
            <div class="col-md-12">
                <div class="chart-box">
                    <canvas id="ltaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- INPUT -->
    <div class="form-wrapper">
        <h5>Input Bulanan</h5>
        <form method="POST" action="{{ route('dashboard.store') }}" class="row g-3">
            @csrf

            <input type="hidden" name="company_id" value="{{ $companyId }}">

            <div class="col-md-3">
                <input type="number" name="month" class="form-control" placeholder="Bulan (1-12)" required>
            </div>
            <div class="col-md-3">
                <input type="number" name="year" class="form-control" placeholder="Tahun" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="man_hours" class="form-control" placeholder="Man Hours" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="total_employee" class="form-control" placeholder="Employee" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="total_lta" class="form-control" placeholder="LTA" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="lost_work_days" class="form-control" placeholder="lost_work_days" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="lost_time" class="form-control" placeholder="lost_time" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="kecelakaan_kerja" class="form-control" placeholder="kecelakaan_kerja" required>
            </div>

            <div class="col-md-12">
                <button class="btn-save">Simpan</button>
            </div>
        </form>
    </div>

</div>

<script>
const labels = @json($chartData['labels']);

new Chart(manHourChart, {
    type: 'line',
    data: {
        labels,
        datasets: [{
            label: 'Man Hours',
            data: @json($chartData['man_hours']),
            borderWidth: 2
        }]
    }
});

new Chart(employeeChart, {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            label: 'Employee',
            data: @json($chartData['employee'])
        }]
    }
});

new Chart(ltaChart, {
    type: 'line',
    data: {
        labels,
        datasets: [{
            label: 'LTA',
            data: @json($chartData['lta']),
            borderWidth: 2
        }]
    }
});
</script>

</body>
</html>
