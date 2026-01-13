@extends('layouts.app')

@section('content')
<div class="container">
    <h3>➕ Input Near Miss</h3>

    <form action="{{ route('near-miss.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Company</label>
            <select name="company_id" class="form-control">
                @foreach($companies as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Period</label>
            <select name="period_id" class="form-control">
                @foreach($periods as $p)
                    <option value="{{ $p->id }}">{{ $p->month }}/{{ $p->year }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Tanggal</label>
            <input type="date" name="date" class="form-control">
        </div>

        <div class="mb-3">
            <label>Lokasi</label>
            <input type="text" name="location" class="form-control">
        </div>

        <div class="mb-3">
            <label>Risk Level</label>
            <select name="risk_level" class="form-control">
                <option>Low</option>
                <option>Medium</option>
                <option>High</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option>Open</option>
                <option>On Progress</option>
                <option>Closed</option>
            </select>
        </div>

        <button class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
