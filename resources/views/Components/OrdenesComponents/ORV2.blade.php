<div class="col-xl-8 col-lg-7">
  <div class="card shadow-sm border-0 rounded-3">

    {{-- ============================================ --}}
    {{-- CABECERA CON ESTADÍSTICAS --}}
    {{-- ============================================ --}}
    <div class="card-header bg-white border-bottom px-4 py-3 rounded-top-3">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h5 class="mb-0 fw-semibold">
          <i class="fas fa-clipboard-list text-primary me-2"></i>Órdenes de Trabajo
          <span class="badge bg-secondary ms-2">
            <i class="fas fa-user me-1"></i>
            {{ ucfirst($departamentoActual) }}
          </span>
        </h5>
      </div>
    </div>

    <div class="card-body p-0">

      {{-- ============================================ --}}
      {{-- FILTROS --}}
      {{-- ============================================ --}}
      <div class="card shadow-sm border-0 m-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <h6 class="mb-0">
            <i class="fas fa-filter text-primary me-2"></i>Filtros
          </h6>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" wire:click="limpiarFiltros">
              <i class="fas fa-undo me-1"></i>Limpiar
            </button>
            @auth
            <button class="btn btn-success btn-sm" wire:click="exportarExcel">
              <i class="fas fa-file-excel me-1"></i>Excel
            </button>
            @endauth
          </div>
        </div>

        <div class="card-body">
          {{-- Primera fila --}}
          <div class="row g-3 align-items-end">
            <div class="col-xl-5 col-lg-12">
              <label class="form-label fw-semibold">Buscar</label>
              <div class="input-group">
                <span class="input-group-text bg-white">
                  <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" class="form-control"
                  placeholder="Folio, descripción, empleado o máquina..."
                  wire:model.live.debounce.400ms="search">
              </div>
            </div>

            <div class="col-xl-3 col-lg-4">
              <label class="form-label fw-semibold">Área</label>
              <select class="form-select" wire:model.live="filtroArea">
                <option value="">Todas las áreas</option>
                @foreach($areasDisponibles as $area)
                <option value="{{ $area->IdArea }}">{{ $area->Nombre }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-xl-2 col-lg-4">
              <label class="form-label fw-semibold">Estado</label>
              <select class="form-select" wire:model.live="filtroEstado">
                <option value="">Todos</option>
                <option value="abierta">Abierta</option>
                <option value="en_proceso">En proceso</option>
                <option value="cerrada">Cerrada</option>
              </select>
            </div>

            <div class="col-xl-2 col-lg-4">
              <label class="form-label fw-semibold">Paro</label>
              <select class="form-select" wire:model.live="filtroParoLinea">
                <option value="">Todos</option>
                <option value="1">Con paro</option>
                <option value="0">Sin paro</option>
              </select>
            </div>
          </div>

          {{-- Segunda fila --}}
          <div class="row g-3 align-items-end mt-1">
            <div class="col-md-3">
              <label class="form-label fw-semibold">Fecha inicio</label>
              <input type="date" class="form-control" wire:model.live="filtroFechaInicio">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Fecha fin</label>
              <input type="date" class="form-control" wire:model.live="filtroFechaFin">
            </div>
          </div>
        </div>
      </div>

      {{-- ============================================ --}}
      {{-- TABLA DE ÓRDENES --}}
      {{-- ============================================ --}}
      <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light sticky-top">
            <tr class="small text-uppercase">
              <th class="ps-4">Folio</th>
              <th>Reporta / Máquina</th>
              <th>Problema / Ubicación</th>
              <th class="text-center">Tipo</th>
              <th class="text-center">Estado</th>
              <th class="text-center">Paro</th>
              <th class="text-center">Tiempo solución</th>
              <th class="text-center pe-4">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($ordenes as $o)
            @php
            // Permisos por fila
            $puedeCerrar = in_array($departamentoActual, ['Mantenimiento', 'IT']) && in_array($o->Status, ['abierta', 'en_proceso']);
            $puedeAprobar = in_array($departamentoActual, ['Produccion']) && $o->Status === 'cerrada' && !$o->HoraRecepcionLinea;
            $puedeVerMateriales = in_array($departamentoActual, ['Mantenimiento', 'IT']) && $o->movimientos->count() > 0;
            $esIT = $departamentoActual === 'IT';
            $puedeEditar = $esIT && $o->Status !== 'cerrada';
            $puedeExportar = $o->HoraArranque !== null;

            $tipoMostrar = $o->Tipo ?? 'correctivo';
            if ($tipoMostrar === 'Otro' && $o->Otro) {
            $tipoMostrar = $o->Otro;
            }

            // Configuración de estados
            $estadosConfig = [
            'cerrada' => ['bg' => '#d1fae5', 'color' => '#065f46', 'icon' => 'check-circle', 'text' => 'Cerrada'],
            'en_proceso' => ['bg' => '#dbeafe', 'color' => '#1e40af', 'icon' => 'spinner', 'text' => 'Proceso'],
            'abierta' => ['bg' => '#fef3c7', 'color' => '#92400e', 'icon' => 'clock', 'text' => 'Abierta'],
            ];
            $estado = $estadosConfig[$o->Status] ?? $estadosConfig['abierta'];
            @endphp

            <tr class="border-bottom hover-shadow transition">

              {{-- Folio y fecha --}}
              <td class="ps-4 py-3">
                <div class="fw-semibold text-primary">{{ $o->Folio }}</div>
                <div class="small text-muted">
                  <i class="far fa-calendar-alt me-1"></i>
                  {{ $o->HoraApertura ? $o->HoraApertura->format('d/m/Y H:i') : 'N/A' }}
                </div>
              </td>

              {{-- Reporta / Máquina --}}
              <td class="py-3">
                <div class="fw-semibold">{{ $o->empleado->Nombre ?? 'N/A' }}</div>
                <div class="small text-muted">
                  <i class="fas fa-id-badge me-1"></i>{{ $o->NumeroEmpleado }}
                </div>
                <div class="small mt-1">
                  <i class="fas fa-microchip text-primary me-1"></i>
                  <span class="text-primary">{{ $o->Maquina ?? '-' }}</span>
                </div>
              </td>

              {{-- Problema / Ubicación --}}
              <td class="py-3">
                <div class="text-truncate" style="max-width: 200px;" title="{{ $o->Descripcion }}">
                  <i class="fas fa-align-left text-muted me-1"></i>
                  {{ Str::limit($o->Descripcion, 40) }}
                </div>
                <div class="small text-muted mt-1">
                  <span class="badge bg-light text-dark border">{{ $o->area->Nombre ?? 'N/A' }}</span>
                  <i class="fas fa-arrow-right mx-1 text-muted" style="font-size: 0.6rem;"></i>
                  <span class="badge bg-light text-dark border">{{ $o->linea->Nombre ?? 'N/A' }}</span>
                </div>
              </td>

              {{-- Tipo --}}
              <td class="text-center py-3">
                <span class="badge rounded-pill px-3 py-2"
                  style="background-color: #f3e8ff; color: #7c3aed; font-weight: 500; font-size: 0.7rem;">
                  <i class="fas fa-wrench me-1"></i>{{ ucfirst($tipoMostrar) }}
                </span>
              </td>

              {{-- Estado --}}
              <td class="text-center py-3">
                @if(in_array($departamentoActual, ['Mantenimiento', 'IT']) && $o->Status === 'abierta')
                <button type="button"
                  class="btn btn-sm btn-outline-info rounded-pill px-3 py-1"
                  wire:click="cambiarEstadoOrden({{ $o->IdOrden }}, 'en_proceso')"
                  style="font-size: 0.7rem;">
                  <i class="fas fa-spinner me-1"></i>Iniciar Proceso
                </button>
                @elseif(in_array($departamentoActual, ['Mantenimiento', 'IT']) && $o->Status === 'en_proceso')
                <button type="button"
                  class="btn btn-sm btn-outline-warning rounded-pill px-3 py-1"
                  wire:click="cambiarEstadoOrden({{ $o->IdOrden }}, 'abierta')"
                  style="font-size: 0.7rem;">
                  <i class="fas fa-undo me-1"></i>Revertir
                </button>
                @else
                <span class="badge rounded-pill px-3 py-2"
                  style="background-color: Blue; font-weight: 500;">
                  <i class="fas fa-{{ $estado['icon'] }} me-1"></i>{{ $estado['text'] }}
                </span>
                @endif
              </td>

              {{-- Paro --}}
              <td class="text-center py-3">
                @if($o->ParoLinea)
                <span class="badge rounded-pill px-3 py-2"
                  style="background-color: #fee2e2; color: #991b1b; font-weight: 500;">
                  <i class="fas fa-stop-circle me-1"></i>{{ $o->TiempoMuerto }} min
                </span>
                @else
                <span class="text-muted" style="font-size: 0.8rem;">
                  <i class="fas fa-minus-circle"></i>
                </span>
                @endif
              </td>

              {{-- Tiempo solución --}}
              <td class="text-center py-3">
                <span class="badge rounded-pill px-3 py-2"
                  style="background-color: #e0f2fe; color: #0369a1; font-weight: 500; font-size: 0.75rem;">
                  <i class="fas fa-clock me-1"></i>{{ $o->tiempo_solucion_calculado ?? 'N/A' }}
                </span>
              </td>

              {{-- Acciones --}}
              <td class="text-center pe-4 py-3">
                <div class="d-flex gap-1 justify-content-center flex-wrap">

                  {{-- Cerrar orden (Mantenimiento/IT) --}}
                  @if($puedeCerrar)
                  <button class="btn btn-sm btn-outline-success rounded-circle p-0"
                    style="width: 34px; height: 34px;"
                    wire:click="editar({{ $o->IdOrden }})"
                    title="Cerrar orden">
                    <i class="fas fa-check"></i>
                  </button>
                  @endif

                  {{-- Aprobar arranque (Producción) --}}
                  @auth
                  @if($puedeAprobar)
                  <button class="btn btn-sm btn-outline-primary rounded-circle p-0"
                    style="width: 34px; height: 34px;"
                    wire:click="editar({{ $o->IdOrden }})"
                    title="Aprobar arranque">
                    <i class="fas fa-play"></i>
                  </button>
                  @endif
                  @endauth

                  {{-- Ver materiales --}}
                  @if($puedeVerMateriales)
                  <button class="btn btn-sm btn-outline-info rounded-circle p-0"
                    style="width: 34px; height: 34px;"
                    wire:click="verMovimientos({{ $o->IdOrden }})"
                    title="Ver materiales">
                    <i class="fas fa-box"></i>
                  </button>
                  @endif
                  
                  {{-- Exportar PDF --}}
                  @auth
                  @if($puedeExportar)
                  <button class="btn btn-sm btn-outline-primary rounded-circle p-0"
                    style="width: 34px; height: 34px;"
                    wire:click="exportarPDF({{ $o->IdOrden }})"
                    title="Exportar Word">
                    <i class="fas fa-file-word"></i>
                  </button>
                  @endif
                  @else
                  {{-- Guest solo ve el mensaje según el estado --}}
                  @if($o->Status === 'abierta')
                  <span class="badge bg-warning px-2 py-1" style="font-size: 11px;">
                    <i class="fas fa-clock me-1"></i> En espera
                  </span>
                  @elseif($o->Status === 'en_proceso')
                  <span class="badge bg-info px-2 py-1" style="font-size: 11px;">
                    <i class="fas fa-spinner fa-spin me-1"></i> En proceso
                  </span>
                  @else
                  <span class="badge bg-success px-2 py-1" style="font-size: 11px;">
                    <i class="fas fa-check-circle me-1"></i> Finalizado
                  </span>
                  @endif
                  @endauth
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="text-center py-5 text-muted">
                <div class="d-flex flex-column align-items-center">
                  <i class="fas fa-inbox fa-3x mb-3"></i>
                  <p class="mb-0">No hay órdenes registradas</p>
                  <small class="text-muted">Intenta ajustar los filtros</small>
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Paginación --}}
      <div class="d-flex justify-content-center py-3">
        {{ $ordenes->links() }}
      </div>
    </div>
  </div>
</div>