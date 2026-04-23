<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? 'Aplikasi Berita SMKN 1 Bawang' }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f6f9;
    }
    .navbar-brand {
      font-weight: 700;
    }
    .card-berita {
      transition: 0.2s ease-in-out;
    }
    .card-berita:hover {
      transform: translateY(-2px);
      box-shadow: 0 0.5rem 1rem rgba(0,0,0,.10);
    }
    .deskripsi-ringkas {
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
      <a class="navbar-brand" href="{{ route('berita.index') }}">
        Berita SMKN 1 Bawang
      </a>
    </div>
  </nav>
  <main class="py-4">
    @yield('content')
  </main>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>