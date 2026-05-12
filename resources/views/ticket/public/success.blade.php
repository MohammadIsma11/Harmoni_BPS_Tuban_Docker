@extends('layouts.public')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <div class="card p-5">
                <div class="mb-4">
                    <div class="display-1 text-success mb-3">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2 class="fw-bold">Laporan Terkirim!</h2>
                    <p class="text-muted">Terima kasih telah melaporkan kendala Anda. Tim kami akan segera menindaklanjuti.</p>
                </div>

                <div class="bg-light p-4 rounded-4 mb-4">
                    <div class="small fw-bold text-uppercase text-muted mb-1">ID Pelacakan Anda</div>
                    <div class="h3 fw-bold text-primary mb-0" style="letter-spacing: 2px;">{{ $tracking_id }}</div>
                </div>

                <p class="small text-muted mb-4">Silakan simpan ID di atas untuk melacak status penanganan laporan Anda di menu "Lacak Tiket".</p>

                <div class="d-grid gap-2">
                    <a href="{{ route('ticket.public.track.form') }}" class="btn btn-primary py-3 rounded-pill fw-bold">
                        <i class="fas fa-search me-2"></i> Lacak Sekarang
                    </a>
                    <a href="{{ route('ticket.public.create') }}" class="btn btn-outline-secondary py-3 rounded-pill fw-bold border-0">
                        Buat Laporan Lain
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const myTickets = JSON.parse(localStorage.getItem('sepintu_tickets_v2') || '[]');
        const newId = "{{ $tracking_id }}";
        const newToken = "{{ $token }}";
        
        if (newId && newToken) {
            const exists = myTickets.some(t => t.id === newId);
            if (!exists) {
                myTickets.push({ id: newId, token: newToken });
                localStorage.setItem('sepintu_tickets_v2', JSON.stringify(myTickets));
                console.log('Ticket ID & Token saved to LocalStorage');
            }
        }
    });
</script>
@endsection
@endsection
