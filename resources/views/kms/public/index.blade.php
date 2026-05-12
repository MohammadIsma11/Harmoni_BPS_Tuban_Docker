@extends('layouts.public')

@section('content')
<div class="container">
    <div class="row justify-content-center mb-5">
        <div class="col-md-8 text-center">
            <h2 class="fw-bold text-primary mb-3">Knowledge Base</h2>
            <p class="text-muted">Temukan jawaban dan panduan seputar layanan BPS Kabupaten Tuban</p>
            
            <form action="{{ route('kms.public.index') }}" method="GET" class="mt-4">
                <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden border">
                    <span class="input-group-text bg-white border-0 ps-4">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" name="q" class="form-control border-0 px-3" placeholder="Cari panduan atau artikel..." value="{{ request('q') }}">
                    <button class="btn btn-primary px-4 fw-bold" type="submit">Cari</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        {{-- Sidebar Kategori --}}
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm p-3">
                <h6 class="fw-bold mb-3 px-2">Kategori</h6>
                <div class="list-group list-group-flush">
                    <a href="{{ route('kms.public.index') }}" class="list-group-item list-group-item-action border-0 rounded-3 mb-1 {{ !request('category') ? 'active' : '' }}">
                        Semua Artikel
                    </a>
                    @foreach($categories as $cat)
                    <a href="{{ route('kms.public.index', ['category' => $cat->slug]) }}" class="list-group-item list-group-item-action border-0 rounded-3 mb-1 d-flex justify-content-between align-items-center">
                        {{ $cat->name }}
                        <span class="badge bg-light text-muted rounded-pill">{{ $cat->articles_count }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Daftar Artikel --}}
        <div class="col-lg-9">
            <div class="row g-4">
                @forelse($articles as $article)
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm hover-up">
                        <div class="card-body p-4">
                            <div class="mb-2">
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3">{{ $article->category->name }}</span>
                            </div>
                            <h5 class="fw-bold mb-3">
                                <a href="{{ route('kms.public.show', $article->slug) }}" class="text-decoration-none text-dark">
                                    {{ $article->title }}
                                </a>
                            </h5>
                            <p class="text-muted small mb-4">
                                {{ Str::limit(strip_tags($article->content), 120) }}
                            </p>
                            <div class="d-flex align-items-center justify-content-between mt-auto">
                                <small class="text-muted"><i class="far fa-calendar me-1"></i> {{ $article->created_at->format('d M Y') }}</small>
                                <a href="{{ route('kms.public.show', $article->slug) }}" class="btn btn-link text-primary p-0 fw-bold text-decoration-none">
                                    Baca Selengkapnya <i class="fas fa-chevron-right ms-1 small"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <img src="https://illustrations.popsy.co/amber/waiting-for-customer.svg" alt="Empty" style="height: 200px;" class="mb-4">
                    <h5 class="text-muted">Belum ada artikel yang tersedia.</h5>
                </div>
                @endforelse
            </div>

            <div class="mt-5">
                {{ $articles->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    .hover-up { transition: all 0.3s; }
    .hover-up:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important; }
    .bg-primary-subtle { background-color: #eef6ff; }
</style>
@endsection
