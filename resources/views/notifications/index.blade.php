@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Arsip Notifikasi</h4>
                <p class="text-muted small mb-0">Riwayat pemberitahuan penugasan dan aktivitas sistem.</p>
            </div>
            @if(Auth::user()->unreadNotifications->count() > 0)
                <a href="{{ route('notifications.readAll') }}" class="btn btn-outline-primary btn-sm rounded-3">
                    <i class="fas fa-check-double me-2"></i>Tandai Semua Sudah Baca
                </a>
            @endif
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="list-group list-group-flush">
                @forelse($notifications as $notification)
                    <div class="list-group-item p-4 border-bottom {{ $notification->read_at ? 'opacity-75' : 'bg-light-primary' }}">
                        <div class="d-flex align-items-start">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-4">
                                <i class="fas fa-bell fs-4 text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold mb-0 text-dark">{{ $notification->data['title'] ?? 'Notifikasi Penugasan' }}</h6>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="text-muted mb-3">{{ $notification->data['message'] ?? '' }}</p>
                                
                                <div class="d-flex align-items-center gap-2">
                                    @if(isset($notification->data['url']))
                                        <a href="{{ route('notifications.markAsRead', $notification->id) }}" class="btn btn-primary btn-sm px-3 rounded-3">
                                            Lihat Detail
                                        </a>
                                    @endif
                                    
                                    @if(!$notification->read_at)
                                        <span class="badge bg-danger rounded-pill px-3 py-2" style="font-size: 0.65rem;">Belum Dibaca</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-5 text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-bell-slash text-muted opacity-25" style="font-size: 5rem;"></i>
                        </div>
                        <h5 class="text-muted fw-bold">Belum Ada Notifikasi</h5>
                        <p class="text-muted small">Anda akan menerima pemberitahuan di sini saat ada penugasan baru.</p>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary px-4 rounded-pill mt-2">
                            Kembali ke Dashboard
                        </a>
                    </div>
                @endforelse
            </div>
            
            @if($notifications->hasPages())
                <div class="card-footer bg-white border-0 p-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .bg-light-primary {
        background-color: rgba(0, 88, 168, 0.05) !important;
        border-left: 4px solid var(--bps-blue);
    }
    .list-group-item {
        transition: all 0.3s;
        border-left: 4px solid transparent;
    }
    .list-group-item:hover {
        background-color: #f8fafc;
    }
</style>
@endpush
