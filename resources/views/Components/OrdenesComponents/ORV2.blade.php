<div class="col-xl-8 col-lg-7">
  <div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white border-bottom px-4 py-3 rounded-top-3">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h5 class="mb-0 fw-semibold">
          <i class="fas fa-clipboard-list text-primary me-2"></i>Órdenes de Trabajo
          <span class="badge bg-secondary ms-2">
            <i class="fas fa-user me-1"></i>
            @auth
            {{ ucfirst($departamentoActual) }}
            @else
            Empleado
            @endauth
          </span>
        </h5>
        <div class="d-flex gap-2">
          <div class="text-center px-2 py-1 bg-light rounded">
            <div class="small text-muted">Total</div>
            <div class="fw-bold">{{ $stats['total'] }}</div>
          </div>
          <div class="text-center px-2 py-1 bg-warning bg-opacity-10 rounded">
            <div class="small text-warning">Abiertas</div>
            <div class="fw-bold text-warning">{{ $stats['abiertas'] }}</div>
          </div>
          <div class="text-center px-2 py-1 bg-info bg-opacity-10 rounded">
            <div class="small text-info">Proceso</div>
            <div class="fw-bold text-info">{{ $stats['en_proceso'] }}</div>
          </div>
          <div class="text-center px-2 py-1 bg-success bg-opacity-10 rounded">
            <div class="small text-success">Cerradas</div>
            <div class="fw-bold text-success">{{ $stats['cerradas'] }}</div>
          </div>
          @if($stats['tiempo_total_muerto'] > 0)
          <div class="text-center px-2 py-1 bg-danger bg-opacity-10 rounded">
            <div class="small text-danger">Tiempo muerto</div>
            <div class="fw-bold text-danger">{{ number_format($stats['tiempo_total_muerto']) }} min</div>
          </div>
          @endif
        </div>
      </div>
    </div>

    <div class="card-body p-0">
      <!-- FILTROS -->
      <div class="p-3 border-bottom bg-light">
        <div class="row g-2 align-items-end">
          <div class="col-md-3">
            <label class="form-label small text-muted mb-0">Buscar</label>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0">
                <i class="fas fa-search text-muted"></i>
              </span>
              <input type="text" class="form-control border-start-0"
                placeholder="Folio, máquina..."
                wire:model.live.debounce.300ms="search">
            </div>
          </div>

          <div class="col-md-2">
            <label class="form-label small text-muted mb-0">Área</label>
            <select class="form-select" wire:model.live="filtroArea">
              <option value="">Todas</option>
              @foreach($areas as $area)
              <option value="{{ $area->Nombre }}">{{ $area->Nombre }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-2">
            <label class="form-label small text-muted mb-0">Línea</label>
            <select class="form-select" wire:model.live="filtroLinea" @if(!$filtroArea) disabled @endif>
              <option value="">Todas</option>
              @foreach($lineasFiltro as $linea)
              <option value="{{ $linea->IdLinea }}">{{ $linea->Nombre }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-2">
            <label class="form-label small text-muted mb-0">Máquina</label>
            <select class="form-select" wire:model.live="filtroMaquina" @if(!$filtroLinea) disabled @endif>
              <option value="">Todas</option>
              @foreach($maquinasFiltro as $maquina)
              <option value="{{ $maquina->Nombre }}">{{ $maquina->Nombre }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-1">
            <label class="form-label small text-muted mb-0">Estado</label>
            <select class="form-select" wire:model.live="filtroEstado">
              <option value="">Todos</option>
              <option value="abierta">Abierta</option>
              <option value="en_proceso">Proceso</option>
              <option value="cerrada">Cerrada</option>
            </select>
          </div>

          <div class="col-md-2">
            <div class="d-flex gap-1">
              <button class="btn btn-outline-danger btn-sm flex-grow-1" wire:click="limpiarFiltros" title="Limpiar filtros">
                <i class="fas fa-undo"></i>
              </button>
              <button class="btn btn-success btn-sm" wire:click="exportarExcel" title="Exportar a Excel">
                <i class="fas fa-file-excel"></i>
              </button>
            </div>
          </div>
        </div>

        <div class="row g-2 mt-2">
          <div class="col-md-3">
            <label class="form-label small text-muted mb-0">Fecha inicio</label>
            <input type="date" class="form-control" wire:model.live="filtroFechaInicio">
          </div>
          <div class="col-md-3">
            <label class="form-label small text-muted mb-0">Fecha fin</label>
            <input type="date" class="form-control" wire:model.live="filtroFechaFin">
          </div>
          <div class="col-md-2">
            <label class="form-label small text-muted mb-0">Paro línea</label>
            <select class="form-select" wire:model.live="filtroParoLinea">
              <option value="">Todos</option>
              <option value="1">Con paro</option>
              <option value="0">Sin paro</option>
            </select>
          </div>
        </div>
      </div>

      <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light sticky-top">
            <tr class="small text-uppercase">
              <th class="ps-4" style="width: 10%">Folio</th>
              <th style="width: 18%">Reporta / Máquina</th>
              <th style="width: 22%">Problema / Ubicación</th>
              <th style="width: 8%" class="text-center">Tipo</th>
              <th style="width: 10%" class="text-center">Estado</th>
              <th style="width: 7%" class="text-center">Paro</th>
              <th style="width: 12%" class="text-center">Tiempo solución</th>
              <th style="width: 13%" class="text-center pe-4">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($ordenes as $o)
            <tr class="border-bottom hover-shadow transition">
              <td class="ps-4 py-3">
                <div class="fw-semibold text-primary">{{ $o->Folio }}</div>
                <div class="small text-muted">
                  <i class="far fa-calendar-alt me-1"></i>
                  {{ $o->HoraApertura ? $o->HoraApertura->format('d/m/Y H:i') : 'N/A' }}
                </div>
              </td>

              <td class="py-3">
                <div class="fw-semibold">{{ $o->empleado->Nombre ?? 'N/A' }}</div>
                <div class="small text-muted">
                  <i class="fas fa-id-badge me-1"></i>
                  {{ $o->NumeroEmpleado }}
                </div>
                <div class="small mt-1">
                  @if($o->area)
                  <i class="fas fa-building text-secondary me-1"></i>
                  <span class="text-secondary">{{ $o->area->Nombre }}</span>
                  <span class="mx-1 text-muted">|</span>
                  @endif
                  <i class="fas fa-microchip text-primary me-1"></i>
                  <span class="text-primary">{{ $o->Maquina ?? '-' }}</span>
                </div>
              </td>

              <td class="py-3">
                <div class="text-truncate" style="max-width: 200px;" title="{{ $o->Descripcion }}">
                  <i class="fas fa-align-left text-muted me-1"></i>
                  {{ Str::limit($o->Descripcion, 40) }}
                </div>
                <div class="small text-muted mt-1">
                  <i class="fas fa-map-marker-alt me-1"></i>
                  <span class="badge bg-light text-dark border">{{ $o->area->Nombre ?? 'N/A' }}</span>
                  <i class="fas fa-arrow-right mx-1 text-muted" style="font-size: 0.6rem;"></i>
                  <span class="badge bg-light text-dark border">{{ $o->linea->Nombre ?? 'N/A' }}</span>
                </div>
              </td>

              <td class="text-center py-3">
                @php
                $tipoMostrar = $o->Tipo ?? 'correctivo';
                if($tipoMostrar === 'Otro' && $o->Otro) {
                $tipoMostrar = $o->Otro;
                }
                @endphp
                <span class="badge rounded-pill px-3 py-2" style="background-color: #f3e8ff; color: #7c3aed; font-weight: 500; font-size: 0.7rem;">
                  <i class="fas fa-wrench me-1"></i>
                  {{ ucfirst($tipoMostrar) }}
                </span>
              </td>

              <td class="text-center py-3">
                @if($o->Status === 'cerrada')
                <span class="badge rounded-pill px-3 py-2" style="background-color: #d1fae5; color: #065f46; font-weight: 500;">
                  <i class="fas fa-check-circle me-1"></i> Cerrada
                </span>
                @elseif($o->Status === 'en_proceso')
                <span class="badge rounded-pill px-3 py-2" style="background-color: #dbeafe; color: #1e40af; font-weight: 500;">
                  <i class="fas fa-spinner me-1"></i> Proceso
                </span>
                @else
                <span class="badge rounded-pill px-3 py-2" style="background-color: #fef3c7; color: #92400e; font-weight: 500;">
                  <i class="fas fa-clock me-1"></i> Abierta
                </span>
                @endif
              </td>

              <td class="text-center py-3">
                @if($o->ParoLinea)
                <span class="badge rounded-pill px-3 py-2" style="background-color: #fee2e2; color: #991b1b; font-weight: 500;">
                  <i class="fas fa-stop-circle me-1"></i> {{ $o->TiempoMuerto }} min
                </span>
                @else
                <span class="text-muted" style="font-size: 0.8rem;">
                  <i class="fas fa-minus-circle"></i>
                </span>
                @endif
              </td>

              <td class="text-center py-3">
                <span class="badge rounded-pill px-3 py-2" style="background-color: #e0f2fe; color: #0369a1; font-weight: 500; font-size: 0.75rem;">
                  <i class="fas fa-clock me-1"></i>
                  {{ $o->tiempo_solucion_calculado ?? 'N/A' }}
                </span>
              </td>

              <td class="text-center pe-4 py-3">
                <div class="d-flex gap-1 justify-content-center flex-wrap">

                  @auth
                  <!-- ============================================ -->
                  <!-- MANTENIMIENTO: Cerrar orden (solo órdenes ABIERTAS) -->
                  <!-- ============================================ -->
                  @php
                  $departamentoActual = $this->getDepartamentoUsuario();
                  @endphp

                  @if($departamentoActual === 'Mantenimiento' && $o->Status === 'abierta')
                  <button class="btn btn-sm btn-outline-success rounded-circle p-0"
                    style="width: 34px; height: 34px; transition: all 0.2s;"
                    wire:click="editar({{ $o->IdOrden }})"
                    title="Cerrar orden">
                    <i class="fas fa-check"></i>
                  </button>
                  @endif

                  <!-- ============================================ -->
                  <!-- PRODUCTION: Aprobar arranque (solo órdenes CERRADAS sin recepción) -->
                  <!-- ============================================ -->
                  @if($departamentoActual === 'production')
                  @if($o->Status === 'cerrada' && !$o->HoraRecepcionLinea)
                  <button class="btn btn-sm btn-outline-primary rounded-circle p-0"
                    style="width: 34px; height: 34px; transition: all 0.2s;"
                    wire:click="editar({{ $o->IdOrden }})"
                    title="Aprobar arranque de línea">
                    <i class="fas fa-play"></i>
                  </button>
                  @endif
                  @endif

                  <!-- ============================================ -->
                  <!-- VER MOVIMIENTOS (Mantenimiento e IT) -->
                  <!-- ============================================ -->
                  @if(in_array($departamentoActual, ['Mantenimiento', 'it']) && $o->movimientos->count() > 0)
                  <button class="btn btn-sm btn-outline-info rounded-circle p-0"
                    style="width: 34px; height: 34px; transition: all 0.2s;"
                    wire:click="verMovimientos({{ $o->IdOrden }})"
                    title="Ver materiales">
                    <i class="fas fa-box"></i>
                  </button>
                  @endif

                  <!-- ============================================ -->
                  <!-- IT: Editar y Eliminar -->
                  <!-- ============================================ -->
                  @if($departamentoActual === 'it')
                  @if($o->Status !== 'cerrada')
                  <button class="btn btn-sm btn-outline-success rounded-circle p-0"
                    style="width: 34px; height: 34px; transition: all 0.2s;"
                    wire:click="editar({{ $o->IdOrden }})"
                    title="Editar orden">
                    <i class="fas fa-pen"></i>
                  </button>
                  @endif
                  <button class="btn btn-sm btn-outline-danger rounded-circle p-0"
                    style="width: 34px; height: 34px; transition: all 0.2s;"
                    wire:click="eliminar({{ $o->IdOrden }})"
                    onclick="return confirm('¿Eliminar la orden {{ $o->Folio }}?')"
                    title="Eliminar orden">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  @endif
                  @endauth

                  <!-- ============================================ -->
                  <!-- EXPORTAR PDF (todos los departamentos, solo órdenes cerradas) -->
                  <!-- ============================================ -->
                  @if($o->Status === 'cerrada')
                  <button class="btn btn-sm btn-outline-danger rounded-circle p-0"
                    style="width: 34px; height: 34px; transition: all 0.2s;"
                    wire:click="exportarPDF({{ $o->IdOrden }})"
                    title="Exportar PDF">
                    <i class="fas fa-file-pdf"></i>
                  </button>
                  @endif
                  @if($o->Status !== 'cerrada')
                  @guest
                  <span class="text-muted small">Aun no se ha cerrado la orden</span>
                  @endguest
                  @endif
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="@auth 8 @else 7 @endauth" class="text-center py-5 text-muted">
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
      <div class="d-flex justify-content-center mt-3">
        {{ $ordenes->links() }}
      </div>
    </div>
  </div>
</div>