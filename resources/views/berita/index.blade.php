@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-lg-8">
            <h2 class="fw-bold mb-1">Daftar Berita</h2>
            <p class="text-muted mb-0">
                Data berita diambil dari website SMKN 1 Bawang dan disimpan ke database lokal.
            </p>
        </div>
        <div class="col-lg-4 mt-3 mt-lg-0">
            <div class="d-flex gap-2 justify-content-lg-end">
                <a href="{{ route('berita.sync') }}" class="btn btn-success">
                    Sinkronkan Berita
                </a>
                <form action="{{ route('berita.truncate') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus semua berita?')">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        Hapus Semua
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('berita.index') }}" method="GET">
                <div class="row g-2">
                    <div class="col-md-10">
                        <input type="text" name="keyword" class="form-control" placeholder="Cari judul, deskripsi, atau kategori..." value="{{ $keyword }}">
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @forelse($beritas as $berita)
        <div class="card card-berita border-0 shadow-sm mb-3">
            <div class="card-body">
                <h4 class="fw-bold mb-2">{{ $berita->judul }}</h4>
                <div class="mb-2 text-muted small">
                    <span class="me-3">
                        <strong>Tanggal:</strong> {{ $berita->tanggal_publish ? $berita->tanggal_publish->format('d-m-Y H:i') : '-' }}
                    </span>
                    <span class="me-3">
                        <strong>Author:</strong> {{ $berita->author ?: '-' }}
                    </span>
                </div>
                <div class="mb-2">
                    <strong>Kategori:</strong> <span class="text-primary">{{ $berita->kategori ?: '-' }}</span>
                </div>
                <p class="deskripsi-ringkas text-secondary mb-3">
                    {{ $berita->deskripsi ?: 'Tidak ada deskripsi.' }}
                </p>
                <a href="{{ $berita->link }}" target="_blank" class="btn btn-outline-primary btn-sm">
                    Baca Selengkapnya
                </a>
            </div>
        </div>
    @empty
        <div class="alert alert-secondary text-center shadow-sm">
            Belum ada data berita. Klik tombol <strong>Sinkronkan Berita</strong>.
        </div>
    @endforelse

    <div class="mt-4">
        {{ $beritas->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
