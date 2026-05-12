@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Manajemen Tiket IT</h3>
            <p class="text-muted small">Kelola laporan kendala IT dari internal maupun masyarakat.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('ticket.public.create') }}" target="_blank" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                <i class="fas fa-plus me-2"></i>Buat Baru
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('ticket.admin.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-light text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-light" placeholder="Cari Subjek, ID, atau Pelapor..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select border-light">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select border-light">
                        <option value="">Semua Status</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="onprogress" {{ request('status') == 'onprogress' ? 'selected' : '' }}>Progress</option>
                        <option value="check wa" {{ request('status') == 'check wa' ? 'selected' : '' }}>Check WA</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill flex-grow-1 px-4 fw-bold shadow-sm">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    @if(request()->anyFilled(['search', 'category_id', 'status']))
                        <a href="{{ route('ticket.admin.index') }}" class="btn btn-light rounded-pill px-4" title="Reset Filter">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @php
            $stats = [
                ['label' => 'Total Tiket', 'count' => \App\Models\Ticket::count(), 'icon' => 'fa-ticket-alt', 'color' => 'primary'],
                ['label' => 'Open', 'count' => \App\Models\Ticket::where('status', 'open')->count(), 'icon' => 'fa-envelope-open', 'color' => 'warning'],
                ['label' => 'Assigned', 'count' => \App\Models\Ticket::where('status', 'assigned')->count(), 'icon' => 'fa-user-tag', 'color' => 'info'],
                ['label' => 'On Progress', 'count' => \App\Models\Ticket::where('status', 'onprogress')->count(), 'icon' => 'fa-spinner', 'color' => 'primary'],
                ['label' => 'Check WA', 'count' => \App\Models\Ticket::where('status', 'check wa')->count(), 'icon' => 'fa-comment-alt', 'color' => 'success'],
                ['label' => 'Closed', 'count' => \App\Models\Ticket::where('status', 'closed')->count(), 'icon' => 'fa-check-circle', 'color' => 'secondary'],
            ];
        @endphp
        @foreach($stats as $s)
        <div class="col-md-2" style="flex: 1;">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 0.6rem;">{{ $s['label'] }}</div>
                        <h4 class="fw-bold mb-0">{{ $s['count'] }}</h4>
                    </div>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background-color: rgba({{ $s['color'] == 'primary' ? '13, 110, 253' : ($s['color'] == 'warning' ? '255, 193, 7' : ($s['color'] == 'info' ? '13, 202, 240' : ($s['color'] == 'success' ? '25, 135, 84' : '108, 117, 125'))) }}, 0.1); color: var(--bs-{{ $s['color'] }});">
                        <i class="fas {{ $s['icon'] }} fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-muted small fw-bold">TIKET & PELAPOR</th>
                        <th class="py-3 text-muted small fw-bold">KATEGORI</th>
                        <th class="py-3 text-muted small fw-bold text-center">STATUS</th>
                        <th class="py-3 text-muted small fw-bold">PJ / ASSIGNEE</th>
                        <th class="pe-4 py-3 text-muted small fw-bold text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $ticket->subject }}</div>
                            <div class="d-flex align-items-center mt-1">
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill me-2" style="font-size: 0.65rem;">{{ $ticket->tracking_id }}</span>
                                <small class="text-muted">{{ $ticket->reporter_name }} ({{ $ticket->reporter_organization }})</small>
                            </div>
                        </td>
                        <td>
                            <div class="small text-dark fw-bold">{{ $ticket->category->name }}</div>
                            <div class="small text-muted" style="font-size: 0.65rem;">Priority: {{ strtoupper($ticket->priority) }}</div>
                        </td>
                        <td class="text-center">
                            @php
                                $statusMap = [
                                    'open' => ['color' => 'warning', 'label' => 'OPEN'],
                                    'assigned' => ['color' => 'info', 'label' => 'ASSIGNED'],
                                    'onprogress' => ['color' => 'primary', 'label' => 'PROGRESS'],
                                    'check wa' => ['color' => 'success', 'label' => 'CHECK WA'],
                                    'closed' => ['color' => 'secondary', 'label' => 'CLOSED']
                                ];
                                $st = $statusMap[$ticket->status] ?? ['color' => 'secondary', 'label' => $ticket->status];
                            @endphp
                            <span class="badge bg-{{ $st['color'] }} rounded-pill px-3 py-2 fw-bold shadow-sm" style="font-size: 0.65rem;">
                                {{ $st['label'] }}
                            </span>
                            @if($ticket->status === 'check wa')
                                <div class="mt-1">
                                    <small class="badge bg-light text-dark border rounded-pill" style="font-size: 0.55rem;">
                                        <i class="fab fa-whatsapp text-success me-1"></i>{{ $ticket->wa_status }}
                                    </small>
                                </div>
                            @endif
                        </td>
                        <td>
                            @php
                                $assigneeIds = $ticket->assigned_to_ids ?? [];
                                $assignees = \App\Models\User::whereIn('id', $assigneeIds)->get();
                            @endphp
                            @forelse($assignees as $pj)
                                <span class="badge bg-light text-dark border rounded-pill small mb-1">{{ $pj->nama_lengkap }}</span>
                            @empty
                                <span class="text-muted small italic">Belum ditugaskan</span>
                            @endforelse
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                @if($ticket->status === 'closed' && !$ticket->pushed_to_kms)
                                    <form action="{{ route('ticket.admin.push', $ticket->id) }}" method="POST" onsubmit="return confirm('Push solusi tiket ini ke sistem KMS?')">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3" title="Push ke KMS">
                                            <i class="fas fa-share-square me-1"></i> Push
                                        </button>
                                    </form>
                                @elseif($ticket->status === 'closed' && $ticket->pushed_to_kms)
                                    <span class="badge bg-success text-white rounded-pill px-3 py-2 shadow-sm" style="font-size: 0.65rem;">
                                        <i class="fas fa-check-double me-1"></i> Pushed
                                    </span>
                                @endif
                                <a href="{{ route('ticket.admin.show', $ticket->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class="fas fa-eye me-1"></i> Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fs-1 d-block mb-3 opacity-25"></i>
                            Belum ada tiket yang masuk.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
        <div class="p-4 border-top">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
