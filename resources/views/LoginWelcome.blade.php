<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Sistema de Tickets</title>

  {{-- Bootstrap --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  {{-- Fuente --}}
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

  {{-- Locales --}}
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

  {{-- Livewire --}}
  @livewireStyles

  <style>
    * {
      font-family: 'Inter', -apple-system, system-ui, sans-serif;
    }

    body {
      background: #f8f9fa;
      color: #1a1e24;
      font-size: 14px;
      line-height: 1.5;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    /* ===== NAVBAR MINIMAL ===== */
    .navbar {
      background: #ffffff;
      border-bottom: 1px solid #e9ecef;
      padding: 0.75rem 0;
    }

    .navbar-brand {
      font-size: 0.9375rem;
      font-weight: 600;
      color: #1a1e24;
      letter-spacing: -0.3px;
    }

    .navbar-brand i {
      color: #4f46e5;
      font-size: 1rem;
      margin-right: 0.5rem;
    }

    .navbar-brand small {
      font-size: 0.6875rem;
      font-weight: 400;
      color: #8a99a8;
    }

    /* ===== MAIN ===== */
    main {
      flex: 1;
      padding: 1.5rem 0;
    }

    .container {
      max-width: 1400px;
      padding: 0 1.5rem;
    }

    /* ===== FOOTER MINIMAL ===== */
    .footer {
      background: #ffffff;
      border-top: 1px solid #e9ecef;
      padding: 0.75rem 0;
      font-size: 0.75rem;
      color: #8a99a8;
    }

    /* ===== UTILIDADES ===== */
    .text-secondary-custom {
      color: #8a99a8;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
      main {
        padding: 1rem 0;
      }

      .container {
        padding: 0 1rem;
      }
    }
  </style>
</head>

<body>

  {{-- ===== NAVBAR ===== --}}
  <nav class="navbar">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="/">
        <i class="bi bi-ticket-perforated"></i>
        <div>
          Tickets
          <div class="small">Sistema interno</div>
        </div>
      </a>
    </div>
  </nav>

  {{-- ===== MAIN CONTENT ===== --}}
  <main>
    <div class="container">
      <livewire:global-alert />

      @hasSection('content')
      @yield('content')
      @else
      {{-- EMPTY STATE --}}
      <div class="text-center py-5">
        <i class="bi bi-inbox fs-1 text-secondary-custom mb-3 d-block"></i>
        <h6 class="fw-semibold mb-1">404</h6>
        <p class="small text-secondary-custom mb-0">Página no encontrada</p>
      </div>
      @endif
    </div>
  </main>

  {{-- ===== FOOTER MINIMAL ===== --}}
  <footer class="footer">
    <div class="container">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
        <div class="d-flex gap-3">
          <span>Sistema de Tickets</span>
          <span class="text-secondary-custom">·</span>
          <span class="text-secondary-custom">v1.0</span>
        </div>
        <div class="text-secondary-custom">
          {{ now()->format('d/m/Y') }}
        </div>
      </div>
    </div>
  </footer>

  {{-- JS --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  {{-- Livewire --}}
  @livewireScripts
</body>

</html>