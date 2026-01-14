@extends('layouts.app')

@section('content')
<div class="container">

    <h3>Near Miss Dashboard</h3>

    @if($stat)
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <small>Total Near Miss</small>
                    <h4>{{ $stat->total_near_miss }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <small>Near Miss Rate</small>
                    <h4>{{ $stat->near_miss_rate }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <small>Open</small>
                    <h4 class="text-warning">{{ $stat->open }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <small>Closed</small>
                    <h4 class="text-success">{{ $stat->closed }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Pie Severity --}}
    <div class="row">
        <div class="col-md-6">
            <h6>Severity</h6>
            <ul>
                <li>High: {{ $stat->high_severity }}</li>
                <li>Medium: {{ $stat->medium_severity }}</li>
                <li>Low: {{ $stat->low_severity }}</li>
            </ul>
        </div>

        <div class="col-md-6">
            <h6>Likelihood</h6>
            <ul>
                <li>High: {{ $stat->high_likelihood }}</li>
                <li>Medium: {{ $stat->medium_likelihood }}</li>
                <li>Low: {{ $stat->low_likelihood }}</li>
            </ul>
        </div>
    </div>
    @else
        <p class="text-muted">Data near miss belum tersedia.</p>
    @endif

</div>
@endsection
