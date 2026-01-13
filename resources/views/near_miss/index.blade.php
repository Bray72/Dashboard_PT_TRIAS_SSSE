@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-4">📊 Near Miss Dashboard</h3>

    {{-- SUMMARY --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h6>Total Near Miss</h6>
                    <h3>{{ $total }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h6>High Risk</h6>
                    <h3 class="text-danger">{{ $highRisk }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h6>Open</h6>
                    <h3 class="text-warning">{{ $open }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h6>Closed</h6>
                    <h3 class="text-success">{{ $closed }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Company</th>
                        <th>Lokasi</th>
                        <th>Risk</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($nearMisses as $nm)
                    <tr>
                        <td>{{ $nm->date }}</td>
                        <td>{{ $nm->company->name }}</td>
                        <td>{{ $nm->location }}</td>
                        <td>
                            <span class="badge bg-danger">{{ $nm->risk_level }}</span>
                        </td>
                        <td>{{ $nm->status }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
