<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Sistema de Tickets</title>

  {{-- Bootstrap --}}
  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

  {{-- Fuente --}}
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

  {{-- Locales --}}
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

  {{-- Livewire --}}
  @livewireStyles

</head>

<body class="d-flex flex-column min-vh-100 bg-light" style="font-family: 'Inter', sans-serif; font-size: 14px;">

  <nav class="navbar bg-white border-bottom py-2">
    <div class="container" style="max-width: 1400px;">
      <a class="navbar-brand fw-semibold" href="/" style="font-size: 0.9375rem; letter-spacing: -0.3px;">
        <i class="bi bi-ticket-perforated text-primary me-2"></i>
        Tickets
        <small class="d-block text-muted fw-normal" style="font-size: 0.6875rem;">Sistema interno</small>
      </a>
    </div>
  </nav>

  <main class="flex-fill py-3">
    <div class="container" style="max-width: 1400px;">
      <livewire:global-alert />

      @hasSection('content')
      @yield('content')
      @else
      <div class="text-center py-5">
        <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
        <h6 class="fw-semibold mb-1">404</h6>
        <p class="small text-muted mb-0">Página no encontrada</p>
      </div>
      @endif
    </div>
  </main>

  <footer class="bg-white border-top py-2 small text-muted">
    <div class="container d-flex justify-content-between align-items-center" style="max-width: 1400px;">
      <div class="d-flex gap-2">
        <span>Sistema de Tickets</span>
        <span>·</span>
        <span>v1.0</span>
      </div>
      <span>{{ now()->format('d/m/Y') }}</span>
    </div>
  </footer>

  <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
  @livewireScripts
</body>

</html>