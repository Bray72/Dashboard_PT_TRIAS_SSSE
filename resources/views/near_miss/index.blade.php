@extends('layouts.app')

@section('content')
<div class="container">

<h4 class="mb-3">Near Miss Dashboard</h4>

{{-- FILTER PERIODE --}}
<form method="GET" class="mb-4">
    <select name="period_id" onchange="this.form.submit()" class="form-select w-25">
        @foreach($periods as $p)
            <option value="{{ $p->id }}" {{ $periodId==$p->id?'selected':'' }}>
                {{ $p->month }}/{{ $p->year }}
            </option>
        @endforeach
    </select>
</form>

{{-- SUMMARY --}}
@if($stat)
<div class="row mb-4">
    <div class="col-md-3"><div class="card p-3">Total<br><b>{{ $stat->total_near_miss }}</b></div></div>
    <div class="col-md-3"><div class="card p-3">Rate<br><b>{{ $stat->near_miss_rate }}</b></div></div>
    <div class="col-md-3"><div class="card p-3 text-warning">Open<br><b>{{ $stat->open }}</b></div></div>
    <div class="col-md-3"><div class="card p-3 text-success">Closed<br><b>{{ $stat->closed }}</b></div></div>
</div>

{{-- CHART --}}
<div class="row mb-5">
    <div class="col-md-6">
        <canvas id="severityChart"></canvas>
    </div>
    <div class="col-md-6">
        <canvas id="likelihoodChart"></canvas>
    </div>
</div>
@endif

<hr>

{{-- FORM INPUT --}}
<h5>Input Near Miss</h5>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('near-miss.store') }}" class="row g-3">
@csrf
<input type="hidden" name="period_id" value="{{ $periodId }}">
<div class="col-md-4">
    <label>Department</label>
    <select name="department_id" class="form-select" required>
        <option value="">-- Pilih Department --</option>
        @foreach($departments as $dept)
            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-md-4">
    <label>Tanggal</label>
    <input type="date" name="date" class="form-control">
</div>

<div class="col-md-4">
    <label>Severity</label>
    <select name="severity" class="form-select">
        <option value="low">Low</option>
        <option value="medium">Medium</option>
        <option value="high">High</option>
    </select>
</div>

<div class="col-md-4">
    <label>Likelihood</label>
    <select name="likelihood" class="form-select">
        <option value="low">Low</option>
        <option value="medium">Medium</option>
        <option value="high">High</option>
    </select>
</div>

<div class="col-md-4">
    <label>Risk Level</label>
    <select name="risk_level" class="form-select">
        <option value="low">Low</option>
        <option value="medium">Medium</option>
        <option value="high">High</option>
    </select>
</div>

<div class="col-12">
    <button class="btn btn-primary">Simpan</button>
</div>
</form>

</div>
@endsection

@section('scripts')
@if($stat)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('severityChart'), {
    type: 'pie',
    data: {
        labels: ['High','Medium','Low'],
        datasets: [{
            data: [
                {{ $stat->high_severity }},
                {{ $stat->medium_severity }},
                {{ $stat->low_severity }}
            ]
        }]
    }
});

new Chart(document.getElementById('likelihoodChart'), {
    type: 'pie',
    data: {
        labels: ['High','Medium','Low'],
        datasets: [{
            data: [
                {{ $stat->high_likelihood }},
                {{ $stat->medium_likelihood }},
                {{ $stat->low_likelihood }}
            ]
        }]
    }
});
</script>
@endif
@endsection
