@extends('layouts.app')

@section('content')
    <h1>Buat Temujanji</h1>

    <div class="card card-primary">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-calendar-plus"></i> Buat Temujanji
            </div>
        </div>
        <form action="{{ route('appointments.store') }}" method="POST">
            <div class="card-body">
                @csrf
                <div class="form-group">
                    <label for="purpose">Tujuan</label>
                    <input type="text" name="purpose" id="purpose"
                        class="form-control @error('purpose') is-invalid @enderror" required>
                    @error('purpose')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="notes">Catatan</label>
                    <textarea name="notes" id="notes" class="form-control" required></textarea>
                </div>


            </div>
            <div class="card-footer d-flex justify-content-end">
                <a href="{{ route('appointments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>&nbsp;
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Simpan Permohonan
                </button>
            </div>
        </form>
    </div>
@endsection
