@extends('layouts.app')

@section('content')
<div class="container">

{{-- ===== DASHBOARD ===== --}}
<h3>Dashboard Near Miss {{ $year }}</h3>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card p-3">
            <h6>Total Near Miss</h6>
            <h3>{{ $totalNearMiss }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <h6>Near Miss Rate</h6>
            <h5>{{ number_format($nearMissRate, 6) }}</h5>
        </div>
    </div>
</div>

<div class="card mb-4 p-3">
    <h6>Risk Level</h6>
    <ul>
        <li>High: {{ $risk['High'] ?? 0 }}</li>
        <li>Medium: {{ $risk['Medium'] ?? 0 }}</li>
        <li>Low: {{ $risk['Low'] ?? 0 }}</li>
    </ul>
</div>

<hr>

{{-- ===== FORM INPUT ===== --}}
<h4>Form Input Near Miss</h4>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('near-miss.department.store') }}">
@csrf

<div class="row">
    <div class="col-md-4 mb-3">
        <label>Tanggal</label>
        <input type="date" name="date" class="form-control" required>
    </div>

    <div class="col-md-4 mb-3">
        <label>Department</label>
        <select name="department_id" class="form-control" required>
            <option value="">-- pilih --</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4 mb-3">
        <label>Lokasi</label>
        <input type="text" name="location" class="form-control" required>
    </div>

    <div class="col-md-4 mb-3">
        <label>Kategori</label>
        <select name="category" class="form-control" required>
            <option>Unsafe Act</option>
            <option>Unsafe Condition</option>
        </select>
    </div>

    <div class="col-md-4 mb-3">
        <label>Severity</label>
        <select name="severity" class="form-control" required>
            <option>Low</option>
            <option>Medium</option>
            <option>High</option>
        </select>
    </div>

    <div class="col-md-4 mb-3">
        <label>Likelihood</label>
        <select name="likelihood" class="form-control" required>
            <option>Low</option>
            <option>Medium</option>
            <option>High</option>
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label>Deskripsi</label>
        <textarea name="description" class="form-control" required></textarea>
    </div>

    <div class="col-md-6 mb-3">
        <label>Tindakan Perbaikan</label>
        <textarea name="action_required" class="form-control"></textarea>
    </div>
</div>

<button class="btn btn-primary">Simpan Near Miss</button>
</form>

</div>
@endsection
