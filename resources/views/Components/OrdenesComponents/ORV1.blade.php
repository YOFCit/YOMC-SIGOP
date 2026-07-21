<div class="col-xl-4 col-lg-5">
  <div style="position: sticky; top: 20px;">
    @if(!$editId)
    <!-- Crear orden - SOLO PRODUCTION -->
    <div class="card shadow-sm border-0 rounded-3">
      <div class="card-header bg-white border-bottom px-4 py-3 rounded-top-3">
        <h5 class="mb-0 fw-semibold">
          <i class="fas fa-plus-circle text-primary me-2"></i>Nueva Orden
        </h5>
      </div>
      <div class="card-body p-4">
        <div class="row g-3">
          <div class="col-6">
            <label class="form-label fw-semibold small">Área <span class="text-danger">*</span></label>
            <select class="form-select rounded-3 @error('IdArea') is-invalid @enderror"
              wire:model.live="IdArea">
              <option value="">Seleccione área</option>
              @foreach($areas as $a)
              <option value="{{ $a->IdArea }}">{{ $a->Nombre }}</option>
              @endforeach
            </select>
            @error('IdArea') <div class="invalid-feedback small">{{ $message }}</div> @enderror
          </div>

          <div class="col-6">
            <label class="form-label fw-semibold small">Línea <span class="text-danger">*</span></label>
            @if($IdArea && $lineasDisponibles->count() > 0)
            <select class="form-select rounded-3 @error('IdLinea') is-invalid @enderror"
              wire:model.live="IdLinea">
              <option value="">Seleccione línea</option>
              @foreach($lineasDisponibles as $l)
              <option value="{{ $l->IdLinea }}">{{ $l->Nombre }}</option>
              @endforeach
            </select>
            @else
            <select class="form-select rounded-3" disabled>
              <option>Primero seleccione área</option>
            </select>
            @endif
            @error('IdLinea') <div class="invalid-feedback small">{{ $message }}</div> @enderror
          </div>

          <div class="col-6">
            <label class="form-label fw-semibold small">
              Máquina/Equipo <span class="text-danger">*</span>
            </label>
            @if($IdLinea && $maquinasDisponibles->count() > 0)
            <select
              class="form-select rounded-3 @error('Maquina') is-invalid @enderror"
              wire:model.live="Maquina">
              <option value="">Seleccione máquina</option>
              @foreach($maquinasDisponibles as $m)
              <option value="{{ $m->Nombre }}">
                {{ $m->Nombre }}
              </option>
              @endforeach
            </select>
            @else
            <select class="form-select rounded-3" disabled>
              <option>Primero seleccione línea</option>
            </select>
            @endif
            @error('Maquina')
            <div class="invalid-feedback small">
              {{ $message }}
            </div>
            @enderror
          </div>

          <div class="col-6 position-relative"
            x-data="{ open: @entangle('mostrarDropdown') }"
            @click.outside="open = false">
            <label class="form-label fw-semibold small">
              Empleado <span class="text-danger">*</span>
            </label>
            <input
              type="text"
              class="form-control rounded-3"
              wire:model.live="busquedaEmpleado"
              @focus="open = true"
              @keydown.escape.window="open = false"
              placeholder="Buscar empleado...">
            <!-- DROPDOWN -->
            <template x-if="open">
              <ul class="list-group position-absolute w-100 shadow" style="z-index: 10;">
                @forelse($empleadosFiltrados as $e)
                <li
                  class="list-group-item list-group-item-action"
                  wire:mousedown="seleccionarEmpleado('{{ $e['NumeroEmpleado'] }}','{{ $e['Nombre'] }}')"
                  @click="open = false"
                  style="cursor:pointer;">
                  {{ $e['NumeroEmpleado'] }} - {{ $e['Nombre'] }}
                </li>
                @empty
                <li class="list-group-item text-muted">
                  Sin resultados
                </li>
                @endforelse
              </ul>
            </template>
            @error('NumeroEmpleado')
            <div class="invalid-feedback small">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Descripción del problema <span class="text-danger">*</span></label>
            <textarea class="form-control rounded-3 @error('Descripcion') is-invalid @enderror"
              rows="3" wire:model="Descripcion"
              placeholder="Describa detalladamente el problema..."></textarea>
            @error('Descripcion') <div class="invalid-feedback small">{{ $message }}</div> @enderror
          </div>
        </div>

        <button class="btn btn-primary w-100 rounded-pill py-2 mt-4" wire:click="guardar">
          <i class="fas fa-save me-1"></i> Crear Orden
        </button>
      </div>
    </div>

    @else
    <!-- ============================================ -->
    <!-- EDITAR ORDEN - PRODUCTION (Aprobar Arranque) -->
    <!-- ============================================ -->
    @if($departamentoActual === 'production')
    <div class="card shadow-sm border-0 rounded-3">
      <div class="card-header bg-white border-bottom px-4 py-3 rounded-top-3">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0 fw-semibold">
            <i class="fas fa-play-circle text-primary me-2"></i>
            Aprobar Arranque
          </h5>
          <span class="badge bg-primary rounded-pill px-3 py-2">{{ $Folio }}</span>
        </div>
      </div>
      <div class="card-body p-4" style="max-height: 730px; overflow-y: auto;">
        <div class="alert alert-info border-0 rounded-3 mb-4">
          <div class="row g-2 small">
            <div class="col-6"><strong>Reportó:</strong> {{ $NombreEmpleado }}</div>
            <div class="col-6"><strong>Máquina:</strong> {{ $Maquina }}</div>
            <div class="col-6"><strong>Área/Línea:</strong> {{ $areaNombre }}/{{ $lineaNombre }}</div>
            <div class="col-12"><strong>Problema:</strong> {{ Str::limit($Descripcion, 50) }}</div>
          </div>
        </div>

        <!-- Campos de tiempo -->
        <div class="row g-3 mb-3">
          <div class="col-6">
            <label class="form-label fw-semibold small">Hora Recepción Línea <span class="text-danger">*</span></label>
            <input type="datetime-local"
              class="form-control rounded-3 @error('HoraRecepcionLinea') is-invalid @enderror"
              wire:model="HoraRecepcionLinea">
            @error('HoraRecepcionLinea')
            <div class="invalid-feedback small">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-6">
            <label class="form-label fw-semibold small">Hora Arranque <span class="text-danger">*</span></label>
            <input type="datetime-local"
              class="form-control rounded-3 @error('HoraArranque') is-invalid @enderror"
              wire:model="HoraArranque">
            @error('HoraArranque')
            <div class="invalid-feedback small">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <!-- Mostrar tiempos calculados -->
        @if($tiempoEspera || $tiempoArranque)
        <div class="alert alert-secondary border-0 rounded-3 mb-3 small">
          <div class="row">
            @if($tiempoEspera)
            <div class="col-6">
              <strong>Tiempo espera:</strong> {{ $tiempoEspera }}
            </div>
            @endif
            @if($tiempoArranque)
            <div class="col-6">
              <strong>Tiempo arranque:</strong> {{ $tiempoArranque }}
            </div>
            @endif
          </div>
        </div>
        @endif

        <!-- Descripción del arranque -->
        <div class="mb-3">
          <label class="form-label fw-semibold">
            Descripción del arranque <span class="text-danger">*</span>
          </label>
          <textarea
            wire:model="DescripcionArranque"
            class="form-control @error('DescripcionArranque') is-invalid @enderror"
            rows="4"
            placeholder="Describe las observaciones del arranque de la línea..."></textarea>
          @error('DescripcionArranque')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Botones de acción -->
        <div class="d-flex gap-2 mt-3">
          <button class="btn btn-primary flex-grow-1 rounded-pill py-2" wire:click="aprobarArranque">
            <i class="fas fa-check-circle me-2"></i>Aprobar Arranque
          </button>
          <button class="btn btn-secondary rounded-pill py-2 px-4" wire:click="limpiar">
            <i class="fas fa-times me-1"></i> Cancelar
          </button>
        </div>
      </div>
    </div>
    @endif

    <!-- ============================================ -->
    <!-- EDITAR ORDEN - MANTENIMIENTO (Cerrar Orden) -->
    <!-- ============================================ -->
    @if($departamentoActual === 'Mantenimiento')
    <div class="card shadow-sm border-0 rounded-3">
      <div class="card-header bg-white border-bottom px-4 py-3 rounded-top-3">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0 fw-semibold">
            <i class="fas fa-check-circle text-success me-2"></i>
            Cerrar Orden
          </h5>
          <span class="badge bg-success rounded-pill px-3 py-2">{{ $Folio }}</span>
        </div>
      </div>
      <div class="card-body p-4" style="max-height: 730px; overflow-y: auto;">
        <div class="alert alert-info border-0 rounded-3 mb-4">
          <div class="row g-2 small">
            <div class="col-6"><strong>Reportó:</strong> {{ $NombreEmpleado }}</div>
            <div class="col-6"><strong>Máquina:</strong> {{ $Maquina }}</div>
            <div class="col-6"><strong>Área/Línea:</strong> {{ $areaNombre }}/{{ $lineaNombre }}</div>
            <div class="col-12"><strong>Problema:</strong> {{ Str::limit($Descripcion, 50) }}</div>
          </div>
        </div>

        <!-- Tipo de Mantenimiento -->
        <div class="mb-3">
          <label class="form-label fw-semibold small">Tipo de Mantenimiento <span class="text-danger">*</span></label>
          <select class="form-select rounded-3 @error('Tipo') is-invalid @enderror"
            wire:model.live="Tipo">
            <option value="" >Seleccione un tipo...</option>
            <option value="correctivo">Correctivo</option>
            <option value="mejora">Mejora</option>
            <option value="instalación">Instalación</option>
            <option value="Otro">Otro</option>
          </select>
          @error('Tipo') <div class="invalid-feedback small">{{ $message }}</div> @enderror
        </div>

        <!-- Campo Otro (solo si aplica) -->
        @if($Tipo === 'Otro')
        <div class="mb-3">
          <label class="form-label fw-semibold small">Especifique el tipo <span class="text-danger">*</span></label>
          <input type="text" class="form-control rounded-3 @error('Otro') is-invalid @enderror"
            wire:model="Otro" placeholder="Ej: Mantenimiento predictivo, Lubricación, etc.">
          @error('Otro') <div class="invalid-feedback small">{{ $message }}</div> @enderror
        </div>
        @endif

        <!-- Procedimiento -->
        <div class="mb-3">
          <label class="form-label fw-semibold small">Procedimiento <span class="text-danger">*</span></label>
          <textarea class="form-control rounded-3 @error('Procedimiento') is-invalid @enderror"
            rows="3" wire:model="Procedimiento"
            placeholder="Describa el procedimiento realizado..."></textarea>
          @error('Procedimiento') <div class="invalid-feedback small">{{ $message }}</div> @enderror
        </div>

        <!-- Paro de línea -->
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" id="paroLinea" wire:model.live="ParoLinea">
          <label class="form-check-label small fw-semibold" for="paroLinea">¿Hubo paro de línea?</label>
        </div>

        @if($ParoLinea)
        <div class="mb-3">
          <label class="form-label fw-semibold small">Tiempo muerto (minutos) <span class="text-danger">*</span></label>
          <input type="number" class="form-control rounded-3 @error('TiempoMuerto') is-invalid @enderror"
            wire:model="TiempoMuerto" placeholder="Minutos" min="0">
          @error('TiempoMuerto') <div class="invalid-feedback small">{{ $message }}</div> @enderror
        </div>
        @endif

        <!-- Materiales -->
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" id="reqMaterial" wire:model.live="ReqMaterial">
          <label class="form-check-label small fw-semibold" for="reqMaterial">¿Se utilizaron materiales?</label>
        </div>

        @if($ReqMaterial)
        <div class="mb-3">
          <label class="form-label fw-semibold small">Materiales utilizados</label>
          @forelse($materialesSeleccionados as $i => $mat)
          <div class="d-flex gap-1 mb-1">
            <select class="form-select form-select-sm rounded-3" wire:model="materialesSeleccionados.{{ $i }}.IdMaterial">
              <option value="">Seleccione material</option>
              @foreach($materiales as $m)
              <option value="{{ $m->IdMaterial }}">{{ $m->Nombre }} (Stock: {{ $m->Stock }})</option>
              @endforeach
            </select>
            <input type="number" class="form-control form-control-sm rounded-3" style="width: 80px;"
              wire:model="materialesSeleccionados.{{ $i }}.CantidadUsada" min="1" placeholder="Cant">
            <button class="btn btn-danger btn-sm" wire:click="removeMaterial({{ $i }})" type="button">
              <i class="fas fa-trash-alt"></i>
            </button>
          </div>
          @empty
          <div class="alert alert-info py-1 small mb-1 rounded-3">No hay materiales agregados</div>
          @endforelse
          <button class="btn btn-sm btn-outline-primary rounded-pill mt-2" type="button" wire:click="addMaterial">
            <i class="fas fa-plus me-1"></i> Agregar material
          </button>
        </div>
        @endif

        <!-- Botón cerrar orden -->
        <div class="d-flex gap-2 mt-3">
          <button class="btn btn-success flex-grow-1 rounded-pill py-2" wire:click="cerrarOrden">
            <i class="fas fa-check-circle me-2"></i>Cerrar Orden
          </button>
          <button class="btn btn-secondary rounded-pill py-2 px-4" wire:click="limpiar">
            <i class="fas fa-times me-1"></i> Cancelar
          </button>
        </div>
      </div>
    </div>
    @endif

    <!-- ============================================ -->
    <!-- EDITAR ORDEN - IT (Edición completa) -->
    <!-- ============================================ -->
    @if($departamentoActual === 'it')
    <div class="card shadow-sm border-0 rounded-3">
      <div class="card-header bg-white border-bottom px-4 py-3 rounded-top-3">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0 fw-semibold">
            <i class="fas fa-edit text-warning me-2"></i>
            Editar Orden - IT
          </h5>
          <span class="badge bg-warning rounded-pill px-3 py-2">{{ $Folio }}</span>
        </div>
      </div>
      <div class="card-body p-4" style="max-height: 730px; overflow-y: auto;">
        <div class="alert alert-info border-0 rounded-3 mb-4">
          <div class="row g-2 small">
            <div class="col-6"><strong>Reportó:</strong> {{ $NombreEmpleado }}</div>
            <div class="col-6"><strong>Máquina:</strong> {{ $Maquina }}</div>
            <div class="col-6"><strong>Área/Línea:</strong> {{ $areaNombre }}/{{ $lineaNombre }}</div>
            <div class="col-12"><strong>Problema:</strong> {{ Str::limit($Descripcion, 50) }}</div>
          </div>
        </div>

        <!-- Estado de la orden -->
        <div class="mb-3">
          <label class="form-label fw-semibold small">Estado de la orden</label>
          <div class="btn-group w-100" role="group">
            <button type="button" class="btn btn-sm {{ $Status === 'abierta' ? 'btn-warning' : 'btn-outline-secondary' }} rounded-start-pill"
              wire:click="cambiarEstado('abierta')">
              <i class="fas fa-play me-1"></i> Abierta
            </button>
            <button type="button" class="btn btn-sm {{ $Status === 'en_proceso' ? 'btn-info' : 'btn-outline-secondary' }}"
              wire:click="cambiarEstado('en_proceso')">
              <i class="fas fa-spinner me-1"></i> Proceso
            </button>
            <button type="button" class="btn btn-sm {{ $Status === 'cerrada' ? 'btn-success' : 'btn-outline-secondary' }} rounded-end-pill"
              wire:click="cambiarEstado('cerrada')">
              <i class="fas fa-check-circle me-1"></i> Cerrada
            </button>
          </div>
          @if($tiempoTranscurrido)
          <div class="mt-2 text-center text-muted small">
            <i class="fas fa-clock me-1"></i> {{ $tiempoTranscurrido }}
          </div>
          @endif
        </div>

        <!-- Tiempo solución -->
        <div class="mb-3">
          <label class="form-label fw-semibold small">Tiempo solución</label>
          <input type="datetime-local"
            class="form-control rounded-3 @error('TiempoSolucion') is-invalid @enderror"
            wire:model="TiempoSolucion">
          @error('TiempoSolucion')
          <div class="invalid-feedback small">{{ $message }}</div>
          @enderror
        </div>

        <!-- Procedimiento -->
        <div class="mb-3">
          <label class="form-label fw-semibold small">Procedimiento</label>
          <textarea class="form-control rounded-3" rows="2" wire:model="Procedimiento"
            placeholder="Describa el procedimiento..."></textarea>
        </div>

        <div class="d-flex gap-2 mt-3">
          <button class="btn btn-primary flex-grow-1 rounded-pill py-2" wire:click="actualizarMtto">
            <i class="fas fa-save me-1"></i> Guardar cambios
          </button>
          <button class="btn btn-secondary rounded-pill py-2 px-4" wire:click="limpiar">
            <i class="fas fa-times me-1"></i> Cancelar
          </button>
        </div>
      </div>
    </div>
    @endif
    @endif
  </div>
</div>