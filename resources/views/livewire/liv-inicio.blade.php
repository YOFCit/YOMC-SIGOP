<div class="container-fluid px-2 px-md-3">
  {{-- ==========================================
        DASHBOARD ADMINISTRADOR
    =========================================== --}}
  @if($esAdmin)

  {{-- Header compacto --}}
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 mb-2">
    <div>
      <h5 class="fw-bold mb-0">Dashboard</h5>
      <small class="text-muted">{{ $this->cargarNombre() }}</small>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-{{ $cargando ? 'warning' : 'success' }} py-1">
        <i class="bi bi-{{ $cargando ? 'hourglass-split' : 'check-circle' }} me-1"></i>
        {{ $cargando ? 'Cargando...' : 'Actualizado' }}
      </span>
      <button class="btn btn-outline-primary btn-sm py-0 px-2"
        wire:click="actualizarDashboard"
        wire:loading.attr="disabled">
        <span wire:loading.remove>
          <i class="bi bi-arrow-clockwise"></i>
        </span>
        <span wire:loading>
          <span class="spinner-border spinner-border-sm" role="status"></span>
        </span>
      </button>
    </div>
  </div>

  {{-- ======================
        KPI CARDS - 4 en fila compactos
    ======================= --}}
  <div class="row g-1 g-md-2 mb-2">
    <div class="col-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body py-2 px-3">
          <small class="text-muted d-block" style="font-size: 0.65rem;">Total</small>
          <span class="fw-bold fs-5">{{ number_format($totalOrdenes) }}</span>
        </div>
      </div>
    </div>
    <div class="col-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body py-2 px-3">
          <small class="text-muted d-block" style="font-size: 0.65rem;">Abiertas</small>
          <span class="fw-bold fs-5 text-warning">{{ number_format($ordenesAbiertas) }}</span>
        </div>
      </div>
    </div>
    <div class="col-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body py-2 px-3">
          <small class="text-muted d-block" style="font-size: 0.65rem;">Proceso</small>
          <span class="fw-bold fs-5 text-info">{{ number_format($ordenesProceso) }}</span>
        </div>
      </div>
    </div>
    <div class="col-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body py-2 px-3">
          <small class="text-muted d-block" style="font-size: 0.65rem;">Cerradas</small>
          <span class="fw-bold fs-5 text-success">{{ number_format($ordenesCerradas) }}</span>
        </div>
      </div>
    </div>
  </div>

  {{-- ======================
        KPI TIEMPOS MUERTOS - 2 en fila compactos
    ======================= --}}
  <div class="row g-1 g-md-2 mb-2">
    <div class="col-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted d-block" style="font-size: 0.65rem;">Total tiempos muertos</small>
            <span class="fw-bold fs-5 text-danger">{{ number_format($totalTiempoMuerto, 1) }}h</span>
          </div>
          <span class="badge bg-danger bg-opacity-10 text-danger py-1 px-2" style="font-size: 0.6rem;">horas</span>
        </div>
      </div>
    </div>
    <div class="col-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted d-block" style="font-size: 0.65rem;">Este mes</small>
            <span class="fw-bold fs-5 text-warning">{{ number_format($tiempoMuertoMes, 1) }}h</span>
          </div>
          <span class="badge bg-warning bg-opacity-10 text-warning py-1 px-2" style="font-size: 0.6rem;">{{ now()->format('M') }}</span>
        </div>
      </div>
    </div>
  </div>

  {{-- ======================
        GRÁFICAS - 3 en fila más compactas
    ======================= --}}
  <div class="row g-1 g-md-2">
    {{-- Estado --}}
    <div class="col-12 col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-1 px-2 border-0">
          <small class="fw-bold"><i class="bi bi-pie-chart me-1"></i>Estado</small>
        </div>
        <div class="card-body p-1">
          <div style="height: 180px; position: relative;">
            @if($cargando)
            <div class="d-flex justify-content-center align-items-center h-100">
              <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
            </div>
            @elseif($estadoChart)
            <livewire:livewire-pie-chart
              key="{{ $estadoChart->reactiveKey() }}"
              :pie-chart-model="$estadoChart" />
            @else
            <div class="text-center text-muted">
              <i class="bi bi-bar-chart-line"></i>
              <small>Sin datos</small>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- Areas --}}
    <div class="col-12 col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-1 px-2 border-0">
          <small class="fw-bold"><i class="bi bi-building me-1"></i>Áreas</small>
        </div>
        <div class="card-body p-1">
          <div style="height: 180px; position: relative;">
            @if($cargando)
            <div class="d-flex justify-content-center align-items-center h-100">
              <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
            </div>
            @elseif($areaChart)
            <livewire:livewire-column-chart
              key="{{ $areaChart->reactiveKey() }}"
              :column-chart-model="$areaChart" />
            @else
            <div class="text-center text-muted">
              <i class="bi bi-bar-chart-line"></i>
              <small>Sin datos</small>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- Tendencia --}}
    <div class="col-12 col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-1 px-2 border-0">
          <small class="fw-bold"><i class="bi bi-graph-up me-1"></i>Tendencia</small>
        </div>
        <div class="card-body p-1">
          <div style="height: 180px; position: relative;">
            @if($cargando)
            <div class="d-flex justify-content-center align-items-center h-100">
              <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
            </div>
            @elseif($mesChart)
            <livewire:livewire-line-chart
              key="{{ $mesChart->reactiveKey() }}"
              :line-chart-model="$mesChart" />
            @else
            <div class="text-center text-muted">
              <i class="bi bi-bar-chart-line"></i>
              <small>Sin datos</small>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Tiempos muertos - gráfica más compacta --}}
  <div class="row g-1 g-md-2 mt-1">
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-1 px-2 border-0">
          <small class="fw-bold"><i class="bi bi-clock me-1"></i>Tiempos muertos por mes</small>
        </div>
        <div class="card-body p-1">
          <div style="height: 180px; position: relative;">
            @if($cargando)
            <div class="d-flex justify-content-center align-items-center h-100">
              <div class="spinner-border text-danger spinner-border-sm" role="status"></div>
            </div>
            @elseif($tiemposMuertosChart)
            <livewire:livewire-column-chart
              key="{{ $tiemposMuertosChart->reactiveKey() }}"
              :column-chart-model="$tiemposMuertosChart" />
            @else
            <div class="text-center text-muted">
              <i class="bi bi-clock"></i>
              <small>Sin datos de tiempos muertos</small>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ==========================================
        DASHBOARD USUARIO NORMAL
    =========================================== --}}
  @else
  <div class="row justify-content-center mt-3">
    <div class="col-12 col-md-6">
      <div class="card shadow-sm border-0">
        <div class="card-body text-center py-4">
          <h5 class="fw-bold">Mi Panel</h5>
          <p class="text-muted small">Resumen de mis órdenes</p>
          <div class="row g-1 mt-2">
            <div class="col-3">
              <div class="card border-0 bg-light">
                <div class="card-body py-2">
                  <small class="text-muted d-block" style="font-size: 0.6rem;">Total</small>
                  <span class="fw-bold">{{ $misOrdenes }}</span>
                </div>
              </div>
            </div>
            <div class="col-3">
              <div class="card border-0 bg-warning bg-opacity-10">
                <div class="card-body py-2">
                  <small class="text-muted d-block" style="font-size: 0.6rem;">Pendientes</small>
                  <span class="fw-bold text-warning">{{ $misOrdenesAbiertas }}</span>
                </div>
              </div>
            </div>
            <div class="col-3">
              <div class="card border-0 bg-info bg-opacity-10">
                <div class="card-body py-2">
                  <small class="text-muted d-block" style="font-size: 0.6rem;">Proceso</small>
                  <span class="fw-bold text-info">{{ $misOrdenesProceso }}</span>
                </div>
              </div>
            </div>
            <div class="col-3">
              <div class="card border-0 bg-success bg-opacity-10">
                <div class="card-body py-2">
                  <small class="text-muted d-block" style="font-size: 0.6rem;">Cerradas</small>
                  <span class="fw-bold text-success">{{ $misOrdenesCerradas }}</span>
                </div>
              </div>
            </div>
          </div>
          <hr class="my-2">
          <a href="{{ route('Ordenes') }}" class="btn btn-outline-primary btn-sm w-100">
            <i class="bi bi-list-check me-1"></i>
            Ver mis órdenes
          </a>
        </div>
      </div>
    </div>
  </div>
  @endif

  <style>
    /* Estilos minimalistas */
    .card {
      border-radius: 0.35rem !important;
    }

    .card-header {
      background: transparent !important;
    }

    .card-body {
      padding: 0.25rem !important;
    }

    .card-body.py-2.px-3 {
      padding: 0.35rem 0.75rem !important;
    }

    .card-body.p-1 {
      padding: 0.25rem !important;
    }

    .container-fluid {
      max-height: 100vh;
      overflow-y: auto;
    }

    /* Scrollbar personalizada */
    .container-fluid::-webkit-scrollbar {
      width: 4px;
    }

    .container-fluid::-webkit-scrollbar-track {
      background: transparent;
    }

    .container-fluid::-webkit-scrollbar-thumb {
      background: #ddd;
      border-radius: 4px;
    }

    .container-fluid::-webkit-scrollbar-thumb:hover {
      background: #ccc;
    }

    /* Para Firefox */
    .container-fluid {
      scrollbar-width: thin;
      scrollbar-color: #ddd transparent;
    }
  </style>
</div>