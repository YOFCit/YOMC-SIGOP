<div>

  {{-- BARRA SUPERIOR --}}
  <div class="bg-white border-bottom px-4 py-3 sticky-top shadow-sm">
    <div class="d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center gap-3">
        <div class="bg-primary rounded-3 p-2 text-white d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
          <i class="bi bi-speedometer2 fs-5"></i>
        </div>
        <div>
          <h5 class="fw-bold mb-0">Dashboard</h5>
          <small class="text-muted">
            @if(auth()->check())
            Hola, <strong>{{ $this->cargarNombre() }}</strong>
            <span class="badge bg-secondary-subtle text-secondary ms-1">
              {{ $esAdmin ? 'Administración / Mantenimiento' : 'Personal' }}
            </span>
            @else
            Vista General (Invitado)
            @endif
          </small>
        </div>
      </div>

      <div class="d-flex align-items-center gap-2">
        @if(auth()->check())
        <span class="badge {{ $cargando ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success-emphasis' }} rounded-pill px-3 py-2 d-none d-md-inline-block">
          <i class="bi bi-{{ $cargando ? 'arrow-repeat spinner' : 'check-circle-fill' }} me-1"></i>
          {{ $cargando ? 'Actualizando...' : 'En Vivo' }}
        </span>
        <button class="btn btn-outline-primary btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center"
          wire:click="actualizarDashboard"
          title="Actualizar datos"
          style="width: 32px; height: 32px;">
          <i class="bi bi-arrow-clockwise fs-6"></i>
        </button>
        @else
        <a href="{{ route('login') }}" class="btn btn-primary btn-sm rounded-pill px-3">
          <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar Sesión
        </a>
        @endif
      </div>
    </div>
  </div>

  {{-- CONTENIDO PRINCIPAL --}}
  <div class="container-fluid px-4 mt-4">
    @if($esAdmin || !auth()->check())
    {{-- ========================================== --}}
    {{-- VISTA ADMINISTRADOR / GENERAL              --}}
    {{-- ========================================== --}}

    {{-- FILA 1: KPIs ÓRDENES (4 Columnas Supercompactas) --}}
    <div class="row g-2 mb-2">

      {{-- TOTAL ÓRDENES --}}
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
          <div class="card-body py-1 px-2">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.58rem; letter-spacing: 0.3px; line-height: 1;">Total Órdenes</span>
                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem; line-height: 1.1;">{{ number_format($totalOrdenes) }}</h6>
              </div>
              <div class="bg-primary bg-opacity-10 text-primary rounded-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                <i class="bi bi-clipboard-data" style="font-size: 0.75rem;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- ABIERTAS --}}
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
          <div class="card-body py-1 px-2">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.58rem; letter-spacing: 0.3px; line-height: 1;">Abiertas</span>
                <h6 class="fw-bold mb-0 text-warning" style="font-size: 0.95rem; line-height: 1.1;">{{ number_format($ordenesAbiertas) }}</h6>
              </div>
              <div class="bg-warning bg-opacity-10 text-warning rounded-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                <i class="bi bi-inbox" style="font-size: 0.75rem;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- EN PROCESO --}}
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
          <div class="card-body py-1 px-2">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.58rem; letter-spacing: 0.3px; line-height: 1;">En Proceso</span>
                <h6 class="fw-bold mb-0 text-info" style="font-size: 0.95rem; line-height: 1.1;">{{ number_format($ordenesProceso) }}</h6>
              </div>
              <div class="bg-info bg-opacity-10 text-info rounded-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                <i class="bi bi-arrow-repeat" style="font-size: 0.75rem;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- CERRADAS --}}
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
          <div class="card-body py-1 px-2">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.58rem; letter-spacing: 0.3px; line-height: 1;">Cerradas</span>
                <h6 class="fw-bold mb-0 text-success" style="font-size: 0.95rem; line-height: 1.1;">{{ number_format($ordenesCerradas) }}</h6>
              </div>
              <div class="bg-success bg-opacity-10 text-success rounded-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                <i class="bi bi-check2-circle" style="font-size: 0.75rem;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
    {{-- FILA 2: TIEMPOS MUERTOS & PIE CHART --}}
    <div class="row g-3 mb-4">
      <div class="col-12 col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body p-3 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">T. Muerto Acumulado</span>
                <h3 class="fw-bold mb-0 text-danger mt-1">{{ number_format($totalTiempoMuerto, 1) }}h</h3>
              </div>
              <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-3">
                <i class="bi bi-clock-history fs-4"></i>
              </div>
            </div>
            <small class="text-muted border-top pt-2 mt-2" style="font-size: 0.8rem;">Histórico total registrado</small>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body p-3 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">T. Muerto del Mes</span>
                <h3 class="fw-bold mb-0 text-warning mt-1">{{ number_format($tiempoMuertoMes, 1) }}h</h3>
              </div>
              <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                <i class="bi bi-calendar-minus fs-4"></i>
              </div>
            </div>
            <small class="text-muted border-top pt-2 mt-2" style="font-size: 0.8rem;">Mes actual: {{ now()->format('M Y') }}</small>
          </div>
        </div>
      </div>

      <div class="col-12 col-xl-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-white border-0 pt-3 pb-0">
            <h6 class="fw-bold text-muted text-uppercase mb-0" style="font-size: 0.8rem;">
              <i class="bi bi-pie-chart-fill me-1 text-primary"></i> Distribución por Estado
            </h6>
          </div>
          <div class="card-body p-3 d-flex align-items-center justify-content-center" style="min-height: 180px;">
            @if($cargando)
            <div class="spinner-border text-primary"></div>
            @elseif($estadoChart)
            <div class="w-100" style="height: 180px;">
              <livewire:livewire-pie-chart key="{{ $estadoChart->reactiveKey() }}" :pie-chart-model="$estadoChart" />
            </div>
            @else
            <span class="text-muted">Sin datos disponibles</span>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- FILA 3: GRÁFICAS PRINCIPALES --}}
    <div class="row g-3">
      <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-white border-0 pt-3 pb-0">
            <h6 class="fw-bold text-muted text-uppercase mb-0" style="font-size: 0.8rem;">
              <i class="bi bi-building me-1 text-primary"></i> Órdenes por Área
            </h6>
          </div>
          <div class="card-body p-3 d-flex align-items-center justify-content-center" style="min-height: 280px;">
            @if($cargando)
            <div class="spinner-border text-primary"></div>
            @elseif($areaChart)
            <div class="w-100" style="height: 280px;">
              <livewire:livewire-column-chart key="{{ $areaChart->reactiveKey() }}" :column-chart-model="$areaChart" />
            </div>
            @else
            <span class="text-muted">Sin datos disponibles</span>
            @endif
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-white border-0 pt-3 pb-0">
            <h6 class="fw-bold text-muted text-uppercase mb-0" style="font-size: 0.8rem;">
              <i class="bi bi-graph-up-arrow me-1 text-success"></i> Tendencia Mensual
            </h6>
          </div>
          <div class="card-body p-3 d-flex align-items-center justify-content-center" style="min-height: 280px;">
            @if($cargando)
            <div class="spinner-border text-success"></div>
            @elseif($mesChart)
            <div class="w-100" style="height: 280px;">
              <livewire:livewire-line-chart key="{{ $mesChart->reactiveKey() }}" :line-chart-model="$mesChart" />
            </div>
            @else
            <span class="text-muted">Sin datos disponibles</span>
            @endif
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-white border-0 pt-3 pb-0">
            <h6 class="fw-bold text-muted text-uppercase mb-0" style="font-size: 0.8rem;">
              <i class="bi bi-clock me-1 text-danger"></i> T. Muertos por Mes (Min)
            </h6>
          </div>
          <div class="card-body p-3 d-flex align-items-center justify-content-center" style="min-height: 280px;">
            @if($cargando)
            <div class="spinner-border text-danger"></div>
            @elseif($tiemposMuertosChart)
            <div class="w-100" style="height: 280px;">
              <livewire:livewire-column-chart key="{{ $tiemposMuertosChart->reactiveKey() }}" :column-chart-model="$tiemposMuertosChart" />
            </div>
            @else
            <span class="text-muted">Sin datos disponibles</span>
            @endif
          </div>
        </div>
      </div>
    </div>

    @else
    {{-- ========================================== --}}
    {{-- VISTA EMPLEADO / USUARIO NORMAL            --}}
    {{-- ========================================== --}}
    <div class="row g-3">
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">Mis Órdenes</span>
                <h3 class="fw-bold mb-0 text-dark mt-1">{{ number_format($misOrdenes) }}</h3>
              </div>
              <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                <i class="bi bi-person-badge fs-4"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">Mis Órdenes Abiertas</span>
                <h3 class="fw-bold mb-0 text-warning mt-1">{{ number_format($misOrdenesAbiertas) }}</h3>
              </div>
              <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                <i class="bi bi-hourglass-split fs-4"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">En Proceso</span>
                <h3 class="fw-bold mb-0 text-info mt-1">{{ number_format($misOrdenesProceso) }}</h3>
              </div>
              <div class="bg-info bg-opacity-10 text-info rounded-3 p-3">
                <i class="bi bi-gear-wide-connected fs-4"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">Cerradas / Listas</span>
                <h3 class="fw-bold mb-0 text-success mt-1">{{ number_format($misOrdenesCerradas) }}</h3>
              </div>
              <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                <i class="bi bi-check-circle fs-4"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif

  </div>
  {{-- CSS Adicional para animación de recarga --}}
  <style>
    .spin-animation {
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      100% {
        transform: rotate(360deg);
      }
    }
  </style>
</div>