@extends('layouts.app')

@section('content')
    <h1>Temujanji</h1>
    <div class="card">
        <div class="card-header">
            <div class="card-title">

                Senarai Temujanji
            </div>
            <div class="card-tools">
                <a href="{{ route('appointments.create') }}" class="btn btn-primary">Buat Temujanji</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Bil</th>
                        <th>No Temujanji</th>
                        <th>Nama</th>
                        <th>Tujuan</th>
                        <th>Tarikh</th>
                        <th>Masa</th>
                        <th>Status</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($appointments as $appointment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $appointment->appointment_no }}</td>
                            <td>{{ $appointment->name }}</td>
                            <td>{{ $appointment->purpose }}</td>
                            <td>{{ $appointment->date }}</td>
                            <td>{{ $appointment->time }}</td>
                            <td>
                                <span class="badge {{ $appointment->statusBadgeClass() }}">{{ $appointment->statusText() }}</span>
                            </td>
                            <td>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
