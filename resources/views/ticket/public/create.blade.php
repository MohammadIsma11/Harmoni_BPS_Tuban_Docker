@extends('layouts.public')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card p-4 p-md-5">
                <div class="mb-5">
                    <h1 class="fw-800 text-dark mb-1" style="font-weight: 800; letter-spacing: -1px;">Buat Laporan Baru</h1>
                    <p class="text-muted small fw-bold text-uppercase tracking-widest">IT Support Center BPS Kabupaten Tuban</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-4 mb-4">
                        <ul class="mb-0 small fw-bold">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-4">
                    <div class="alert alert-info border-0 rounded-4 p-4 d-flex align-items-center">
                        <div class="me-3">
                            <i class="fab fa-whatsapp fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Butuh bantuan cepat?</h6>
                            <p class="mb-2 small">Anda juga bisa melaporkan kendala langsung melalui WhatsApp Call Center.</p>
                            <button type="button" onclick="handleWaQuickChat()" class="btn btn-success btn-sm rounded-pill px-3 fw-bold text-uppercase">
                                <i class="fab fa-whatsapp me-1"></i> Chat via WhatsApp
                            </button>
                        </div>
                    </div>
                </div>

                <form action="{{ route('ticket.public.store') }}" method="POST" enctype="multipart/form-data" id="ticket-form">
                    @csrf
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-primary text-uppercase">Nama Pelapor</label>
                            <input type="text" name="reporter_name" id="reporter_name" value="{{ old('reporter_name') }}" required class="form-control rounded-4 p-3 bg-light border-0" placeholder="Nama Lengkap">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-primary text-uppercase">WhatsApp Aktif</label>
                            <input type="text" name="reporter_phone" id="reporter_phone" value="{{ old('reporter_phone') }}" required class="form-control rounded-4 p-3 bg-light border-0" placeholder="0812XXXXXXXX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-primary text-uppercase">Email (Opsional)</label>
                            <input type="email" name="reporter_email" id="reporter_email" value="{{ old('reporter_email') }}" class="form-control rounded-4 p-3 bg-light border-0" placeholder="alamat@email.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-primary text-uppercase">Kirim Notifikasi Melalui:</label>
                            <div class="d-flex gap-3 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="notification_method" id="notif-wa" value="whatsapp" checked>
                                    <label class="form-check-label small fw-bold" for="notif-wa">WhatsApp</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="notification_method" id="notif-email" value="email">
                                    <label class="form-check-label small fw-bold" for="notif-email">Email</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-primary text-uppercase">Instansi / Unit Kerja</label>
                            <input type="text" name="reporter_organization" id="reporter_organization" value="{{ old('reporter_organization') }}" required class="form-control rounded-4 p-3 bg-light border-0" placeholder="Contoh: BPS Kabupaten Tuban">
                        </div>
                    </div>

                    <hr class="my-4 opacity-10">

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-primary text-uppercase">Kategori Masalah</label>
                            <select name="category_id" id="category_id" required class="form-select rounded-4 p-3 bg-light border-0">
                                <option value="" disabled selected>Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" data-name="{{ $category->name }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-primary text-uppercase">Level Urgensi</label>
                            <select name="priority" required class="form-select rounded-4 p-3 bg-light border-0">
                                <option value="rendah" {{ old('priority') == 'rendah' ? 'selected' : '' }}>Rendah (Bisa Ditunggu)</option>
                                <option value="sedang" {{ old('priority', 'sedang') == 'sedang' ? 'selected' : '' }}>Sedang (Normal)</option>
                                <option value="tinggi" {{ old('priority') == 'tinggi' ? 'selected' : '' }}>Tinggi (Mendesak)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-primary text-uppercase">Subjek Laporan</label>
                            <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required class="form-control rounded-4 p-3 bg-light border-0" placeholder="Garis besar kendala...">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-primary text-uppercase">Penjelasan Detail</label>
                            <textarea name="description" id="description" rows="5" required class="form-control rounded-4 p-3 bg-light border-0" placeholder="Jelaskan secara rinci agar cepat ditangani...">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-primary text-uppercase">Lampiran Gambar (Opsional)</label>
                            <div class="p-4 border border-2 border-dashed rounded-4 text-center bg-light">
                                <input type="file" name="attachment" class="form-control bg-transparent border-0">
                            </div>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold text-uppercase tracking-widest shadow-lg">
                            <i class="fas fa-paper-plane me-2"></i> Submit Laporan
                        </button>
                    </div>
                </form>

<script>
function handleWaQuickChat() {
    const name = document.getElementById('reporter_name').value;
    const categorySelect = document.getElementById('category_id');
    const category = categorySelect.options[categorySelect.selectedIndex]?.getAttribute('data-name');
    const subject = document.getElementById('subject').value;
    const description = document.getElementById('description').value;

    // Jika form masih kosong (Nama atau Kategori belum diisi), munculkan modal pilihan seperti di landing page
    if (!name || !categorySelect.value) {
        const waModalElement = document.getElementById('waModal');
        const waModal = new bootstrap.Modal(waModalElement);
        waModal.show();
    } else {
        // Jika sudah diisi, langsung buat template lengkap
        const text = `Halo Call Center BPS Tuban, saya ${name} ingin melaporkan kendala terkait *${category}*.\n\n*Subjek:* ${subject || '-'}\n*Deskripsi:* ${description || '-'}`;
        const encodedText = encodeURIComponent(text);
        window.open(`https://wa.me/6285755461223?text=${encodedText}`, '_blank');
    }
}
</script>
            </div>
        </div>
    </div>
</div>
@endsection
