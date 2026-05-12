@extends('layouts.public')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card p-5">
                <div class="text-center mb-5">
                    <div class="h1 text-primary mb-3"><i class="fas fa-search-location"></i></div>
                    <h2 class="fw-bold">Lacak Status Tiket</h2>
                    <p class="text-muted">Masukkan ID Pelacakan untuk melihat progres penanganan laporan Anda.</p>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger border-0 rounded-4 mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('ticket.public.track') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-muted">ID Pelacakan</label>
                        <input type="text" name="tracking_id" required class="form-control form-control-lg rounded-4 p-3 bg-light border-0 text-center" placeholder="BPS-TBN-XXXX" style="letter-spacing: 2px;">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-lg">
                        <i class="fas fa-search me-2"></i> Cari Tiket
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
