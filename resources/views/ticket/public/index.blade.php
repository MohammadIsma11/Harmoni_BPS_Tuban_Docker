@extends('layouts.public')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="mb-5">
                <h1 class="fw-800 text-dark mb-1" style="font-weight: 800; letter-spacing: -1px;">Tiket Saya</h1>
            <div id="tickets-container">
                <div class="text-center py-5" id="loading-state">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <p class="text-muted">Memuat daftar tiket Anda...</p>
                </div>

                <div class="card border-0 shadow-sm rounded-4 d-none" id="empty-state">
                    <div class="card-body p-5 text-center">
                        <div class="bg-light rounded-circle p-4 d-inline-block mb-3">
                            <i class="fas fa-ticket-alt fa-3x text-muted opacity-25"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Belum Ada Tiket</h5>
                        <p class="text-muted">Anda belum memiliki tiket yang tersimpan di perangkat ini.</p>
                        <a href="{{ route('ticket.public.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold">
                            Buat Tiket Baru
                        </a>
                    </div>
                </div>

                <div class="row g-4 d-none" id="tickets-list">
                    {{-- Tickets will be injected here via JS --}}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('tickets-container');
    const loading = document.getElementById('loading-state');
    const empty = document.getElementById('empty-state');
    const list = document.getElementById('tickets-list');
    const manualForm = document.getElementById('add-manual-form');

    function refreshTickets() {
        const myTickets = JSON.parse(localStorage.getItem('sepintu_tickets_v2') || '[]');
        
        if (myTickets.length === 0) {
            loading.classList.add('d-none');
            empty.classList.remove('d-none');
            list.classList.add('d-none');
            return;
        }

        loading.classList.remove('d-none');
        empty.classList.add('d-none');

        // Build items for AJAX
        const items = myTickets.map(t => `items[][id]=${t.id}&items[][token]=${t.token}`).join('&');

        fetch("{{ route('ticket.public.index') }}?" + items, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(tickets => {
            loading.classList.add('d-none');
            if (tickets.length === 0) {
                empty.classList.remove('d-none');
                list.classList.add('d-none');
            } else {
                list.classList.remove('d-none');
                let html = '';
                tickets.forEach(ticket => {
                    const statusClass = {
                        'open': 'bg-primary',
                        'onprogress': 'bg-warning text-dark',
                        'confirm WA': 'bg-info',
                        'closed': 'bg-success'
                    }[ticket.status] || 'bg-secondary';

                    html += `
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4 hover-up transition-all">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <span class="badge ${statusClass} rounded-pill px-3 py-2 small fw-bold text-uppercase">
                                            ${ticket.status}
                                        </span>
                                        <small class="text-muted fw-bold">${ticket.tracking_id}</small>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-2 text-truncate-2" style="min-height: 3rem;">${ticket.subject}</h6>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-light rounded-3 p-2 me-2">
                                            <i class="fas fa-folder text-primary small"></i>
                                        </div>
                                        <small class="text-muted fw-bold">${ticket.category ? ticket.category.name : 'Uncategorized'}</small>
                                    </div>
                                    <div class="border-top pt-3 mt-auto">
                                        <a href="{{ url('tickets/track') }}?tracking_id=${ticket.tracking_id}" class="btn btn-outline-primary btn-sm w-100 rounded-pill fw-bold">
                                            Detail Progress <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                list.innerHTML = html;
            }
        });
    }

    refreshTickets();
});
</script>

<style>
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.hover-up:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,88,168,0.1) !important;
}
.transition-all {
    transition: all 0.3s ease;
}
</style>
@endsection
