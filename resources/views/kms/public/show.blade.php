@extends('layouts.public')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kms.public.index') }}" class="text-decoration-none">Knowledge Base</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $article->category->name }}</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-body p-lg-5 p-4">
                    <div class="mb-3">
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3">{{ $article->category->name }}</span>
                    </div>
                    <h1 class="fw-bold mb-4">{{ $article->title }}</h1>
                    
                    <div class="d-flex align-items-center mb-5 text-muted small border-bottom pb-4">
                        <div class="d-flex align-items-center me-4">
                            <i class="far fa-user me-2"></i> {{ $article->author->nama_lengkap ?? 'Tim Humas BPS' }}
                        </div>
                        <div class="d-flex align-items-center me-4">
                            <i class="far fa-calendar me-2"></i> {{ $article->created_at->format('d M Y') }}
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="far fa-eye me-2"></i> {{ $article->view_count }}x dilihat
                        </div>
                    </div>

                    <div class="article-content lh-lg">
                        {!! $article->content !!}
                    </div>

                    <div class="mt-5 pt-5 border-top">
                        <h6 class="fw-bold mb-3">Apakah artikel ini membantu?</h6>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary rounded-pill px-4"><i class="far fa-thumbs-up me-2"></i> Ya, sangat membantu</button>
                            <button class="btn btn-outline-secondary rounded-pill px-4"><i class="far fa-thumbs-down me-2"></i> Belum membantu</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-5">
                <a href="{{ route('kms.public.index') }}" class="btn btn-link text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke daftar artikel
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .article-content img { max-width: 100%; height: auto; border-radius: 15px; margin: 1.5rem 0; }
    .bg-primary-subtle { background-color: #eef6ff; }
</style>
@endsection
