<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistema de control</title>

  {{-- Favicon --}}
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

  {{-- Bootstrap 5 --}}
  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
  {{-- ApexCharts --}}
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

  {{-- Google Fonts --}}
  <link href="{{ asset('css/css2.css') }}" rel="stylesheet">

  {{-- Internal styles --}}
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
  <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">
  <link href="{{ asset('css/materiales.css') }}" rel="stylesheet">
  <link href="{{ asset('css/areas.css') }}" rel="stylesheet">
  <link href="{{ asset('css/empleados.css') }}" rel="stylesheet">
  <link href="{{ asset('css/movimientos.css') }}" rel="stylesheet">
  <link href="{{ asset('css/ordenes.css') }}" rel="stylesheet">
  <link href="{{ asset('css/tiempos.css') }}" rel="stylesheet">
  <link href="{{ asset('css/tiempoextra.css') }}" rel="stylesheet">

  @livewireStyles

</head>

<body>

  <nav class="navbar navbar-expand-lg py-2">
    <div class="container-fluid px-3 px-lg-4">
      <a class="navbar-brand" href="{{ route('Home') }}">
        <i class="bi bi-grid-3x3-gap-fill"></i> Sistema de control
      </a>
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
        <i class="bi bi-list"></i>
      </button>
      <div class="collapse navbar-collapse" id="nav">
        <ul class="navbar-nav ms-auto gap-1">

          {{-- ADMINISTRADOR --}}
          @if(auth()->check() && auth()->user()->Position === 'Administrador')
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-shield"></i> Admin
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
              <li><a class="dropdown-item" href="{{ route('Areas') }}"><i class="bi bi-diagram-3"></i> Áreas</a></li>
              <li><a class="dropdown-item" href="{{ route('Usuarios') }}"><i class="bi bi-people"></i> Usuarios</a></li>
              <li><a class="dropdown-item" href="{{ route('Empleados') }}"><i class="bi bi-person-badge"></i> Empleados</a></li>
            </ul>
          </li>
          @endif

          {{-- MATERIALES Y MOVIMIENTOS --}}
          @if(auth()->check() && in_array(auth()->user()->Position, ['Ingeniero', 'Administrador', 'Asistente']))
          <li class="nav-item">
            <a class="nav-link" href="{{ route('Materiales') }}"><i class="bi bi-box"></i> Materiales</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('Movimientos') }}"><i class="bi bi-arrow-left-right"></i> Movimientos</a>
          </li>
          @endif

          {{-- ORDENES --}}
          <li class="nav-item">
            <a class="nav-link" href="{{ route('Ordenes') }}"><i class="bi bi-clipboard"></i> Órdenes</a>
          </li>

          {{-- TIEMPO EXTRA --}}
          <li class="nav-item">
            <a class="nav-link" href="{{ route('Tiempoextra') }}"><i class="bi bi-clock"></i> Extra</a>
          </li>

          {{-- LOGIN --}}
          @if(!Route::is('Home') && !auth()->check())
          <li class="nav-item">
            <a class="nav-link" href="{{ route('login') }}"><i class="bi bi-person-circle"></i> Login</a>
          </li>
          @endif

          {{-- LOGOUT --}}
          @if(auth()->check())
          <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
              @csrf
              <button class="btn btn-link nav-link btn-logout"><i class="bi bi-box-arrow-right"></i> Salir</button>
            </form>
          </li>
          @endif

        </ul>
      </div>
    </div>
  </nav>

  <main class="py-3">
    <div class="container-fluid px-3 px-lg-4">
      <livewire:global-alert />
      @yield('datos')
    </div>
  </main>

  <footer class="footer py-2">
    <div class="container-fluid px-3 px-lg-4 text-center">
      {{ now()->format('d/m/Y') }} · Sistema de Control
    </div>
  </footer>

  <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
  @livewireScripts
  @livewireChartsScripts
</body>

</html>