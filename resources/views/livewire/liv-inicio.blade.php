<div class="container-fluid dashboard-container px-3 px-lg-4 py-2">
  @if($esAdmin)
  {{-- HEADER --}}
  <div class="dashboard-header d-flex flex-wrap justify-content-between align-items-center mb-2">
    <div>
      <h5 class="fw-bold mb-0">
        <i class="bi bi-speedometer2 me-2 text-primary"></i>
        Dashboard
      </h5>
      <small class="text-muted">{{ $this->cargarNombre() }}</small>
    </div>
    <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
      <span class="badge rounded-pill bg-{{ $cargando ? 'warning' : 'success' }} px-3 py-1">
        <i class="bi bi-{{ $cargando ? 'hourglass-split' : 'check-circle' }} me-1"></i>
        {{ $cargando ? 'Actualizando...' : 'Actualizado' }}
      </span>
      <button class="btn btn-outline-primary btn-sm rounded-pill px-3" wire:click="actualizarDashboard" wire:loading.attr="disabled">
        <span wire:loading.remove><i class="bi bi-arrow-clockwise"></i></span>
        <span wire:loading><span class="spinner-border spinner-border-sm"></span></span>
      </button>
    </div>
  </div>
  {{-- KPIS PRINCIPALES (5% más grande) --}}
  <div class="row g-1 mb-2" style="transform:scale(1.05);transform-origin:top left;width:95.24%">
    <div class="col-6 col-md-3">
      <div class="card dashboard-card">
        <div class="card-body py-1 px-2">
          <small class="text-muted" style="font-size:.68rem">Total órdenes</small>
          <div class="kpi-number" style="font-size:1.36rem">{{ number_format($totalOrdenes) }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card dashboard-card border-start border-warning border-4">
        <div class="card-body py-1 px-2">
          <small class="text-muted" style="font-size:.68rem">Abiertas</small>
          <div class="kpi-number text-warning" style="font-size:1.36rem">{{ number_format($ordenesAbiertas) }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card dashboard-card border-start border-info border-4">
        <div class="card-body py-1 px-2">
          <small class="text-muted" style="font-size:.68rem">En proceso</small>
          <div class="kpi-number text-info" style="font-size:1.36rem">{{ number_format($ordenesProceso) }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card dashboard-card border-start border-success border-4">
        <div class="card-body py-1 px-2">
          <small class="text-muted" style="font-size:.68rem">Cerradas</small>
          <div class="kpi-number text-success" style="font-size:1.36rem">{{ number_format($ordenesCerradas) }}</div>
        </div>
      </div>
    </div>
  </div>
  {{-- TIEMPOS MUERTOS (5% más grande) --}}
  <div class="row g-1 mb-2" style="transform:scale(1.05);transform-origin:top left;width:95.24%">
    <div class="col-md-6">
      <div class="card dashboard-card">
        <div class="card-body py-1 px-2 d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted" style="font-size:.68rem">Tiempo muerto acumulado</small>
            <div class="kpi-number text-danger" style="font-size:1.36rem">{{ number_format($totalTiempoMuerto,1) }}h</div>
          </div>
          <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill" style="font-size:.63rem">Horas</span>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card dashboard-card">
        <div class="card-body py-1 px-2 d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted" style="font-size:.68rem">Tiempo muerto del mes</small>
            <div class="kpi-number text-warning" style="font-size:1.36rem">{{ number_format($tiempoMuertoMes,1) }}h</div>
          </div>
          <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill" style="font-size:.63rem">{{ now()->format('M') }}</span>
        </div>
      </div>
    </div>
  </div>
  {{-- GRAFICAS PRINCIPALES --}}
  <div class="row g-2">
    <div class="col-12 col-xl-4">
      <div class="card dashboard-card h-100">
        <div class="card-header py-1 px-2" style="font-size:.8rem"><i class="bi bi-pie-chart me-2"></i>Estado</div>
        <div class="card-body py-1 px-2">
          <div class="chart-container" style="height:180px">
            @if($cargando)
            <div class="loading-chart"><span class="spinner-border text-primary" style="width:1.5rem;height:1.5rem"></span></div>
            @elseif($estadoChart)
            <livewire:livewire-pie-chart key="{{ $estadoChart->reactiveKey() }}" :pie-chart-model="$estadoChart" />
            @else
            <div class="empty-chart">Sin datos</div>
            @endif
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-xl-4">
      <div class="card dashboard-card h-100">
        <div class="card-header py-1 px-2" style="font-size:.8rem"><i class="bi bi-building me-2"></i>Áreas</div>
        <div class="card-body py-1 px-2">
          <div class="chart-container" style="height:180px">
            @if($cargando)
            <div class="loading-chart"><span class="spinner-border text-primary" style="width:1.5rem;height:1.5rem"></span></div>
            @elseif($areaChart)
            <livewire:livewire-column-chart key="{{ $areaChart->reactiveKey() }}" :column-chart-model="$areaChart" />
            @else
            <div class="empty-chart">Sin datos</div>
            @endif
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-xl-4">
      <div class="card dashboard-card h-100">
        <div class="card-header py-1 px-2" style="font-size:.8rem"><i class="bi bi-graph-up me-2"></i>Tendencia</div>
        <div class="card-body py-1 px-2">
          <div class="chart-container" style="height:180px">
            @if($cargando)
            <div class="loading-chart"><span class="spinner-border text-primary" style="width:1.5rem;height:1.5rem"></span></div>
            @elseif($mesChart)
            <livewire:livewire-line-chart key="{{ $mesChart->reactiveKey() }}" :line-chart-model="$mesChart" />
            @else
            <div class="empty-chart">Sin datos</div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
  {{-- TIEMPOS MUERTOS POR MES --}}
  <div class="row g-2 mt-2">
    <div class="col-12">
      <div class="card dashboard-card">
        <div class="card-header py-1 px-2" style="font-size:.8rem"><i class="bi bi-clock me-2"></i>Tiempos muertos por mes</div>
        <div class="card-body py-1 px-2">
          <div class="chart-container chart-large" style="height:200px">
            @if($cargando)
            <div class="loading-chart"><span class="spinner-border text-danger" style="width:1.5rem;height:1.5rem"></span></div>
            @elseif($tiemposMuertosChart)
            <livewire:livewire-column-chart key="{{ $tiemposMuertosChart->reactiveKey() }}" :column-chart-model="$tiemposMuertosChart" />
            @else
            <div class="empty-chart">Sin datos de tiempos muertos</div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
  @else
  {{-- DASHBOARD USUARIO NORMAL (centrado perfecto) --}}
  <div class="row justify-content-center align-items-center" style="min-height:70vh">
    <div class="col-12 col-md-8 col-lg-6 col-xl-5">
      <div class="card dashboard-card shadow-sm">
        <div class="card-body text-center p-4">
          <div class="mb-3">
            <i class="bi bi-person-circle display-4 text-primary"></i>
            <h5 class="fw-bold mt-2">Mi Panel</h5>
            <p class="text-muted mb-0 small">Resumen de mis órdenes asignadas</p>
          </div>
          <div class="row g-2">
            <div class="col-6 col-md-3">
              <div class="mini-card" style="padding:.6rem .3rem">
                <small style="font-size:.65rem">Total</small>
                <strong style="font-size:1.2rem">{{ $misOrdenes }}</strong>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="mini-card warning" style="padding:.6rem .3rem">
                <small style="font-size:.65rem">Pendientes</small>
                <strong style="font-size:1.2rem">{{ $misOrdenesAbiertas }}</strong>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="mini-card info" style="padding:.6rem .3rem">
                <small style="font-size:.65rem">Proceso</small>
                <strong style="font-size:1.2rem">{{ $misOrdenesProceso }}</strong>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="mini-card success" style="padding:.6rem .3rem">
                <small style="font-size:.65rem">Cerradas</small>
                <strong style="font-size:1.2rem">{{ $misOrdenesCerradas }}</strong>
              </div>
            </div>
          </div>
          <hr class="my-3">
          <a href="{{ route('Ordenes') }}" class="btn btn-outline-primary rounded-pill px-4">
            <i class="bi bi-list-check me-2"></i>Ver mis órdenes
          </a>
        </div>
      </div>
    </div>
  </div>
  @endif
  <style>
    .dashboard-container {
      min-height: calc(100vh - 70px)
    }

    .dashboard-card {
      border: 0;
      border-radius: .6rem !important;
      box-shadow: 0 .05rem .2rem rgba(0, 0, 0, .04);
      transition: .1s ease
    }

    .dashboard-card:hover {
      transform: translateY(-1px)
    }

    .dashboard-card .card-header {
      background: white !important;
      border: 0;
      padding: .25rem .5rem !important;
      font-weight: 600
    }

    .dashboard-card .card-body {
      padding: .25rem .5rem !important
    }

    .kpi-number {
      font-weight: 700;
      margin-top: 0
    }

    .mini-card {
      background: #f8f9fa;
      border-radius: .4rem
    }

    .mini-card small {
      display: block;
      color: #6c757d
    }

    .mini-card.warning {
      background: rgba(255, 193, 7, .12)
    }

    .mini-card.info {
      background: rgba(13, 202, 240, .12)
    }

    .mini-card.success {
      background: rgba(25, 135, 84, .12)
    }

    .loading-chart {
      height: 100%;
      display: flex;
      justify-content: center;
      align-items: center
    }

    .empty-chart {
      height: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #888;
      font-size: .75rem
    }

    @media(max-width:768px) {
      .dashboard-container {
        padding-left: .5rem !important;
        padding-right: .5rem !important
      }
    }

    .dashboard-container::-webkit-scrollbar {
      width: 4px
    }

    .dashboard-container::-webkit-scrollbar-thumb {
      background: #ddd;
      border-radius: 10px
    }
  </style>
</div>