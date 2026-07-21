<div class="col-xl-4 col-lg-5">
  <div class="card border-0 shadow-sm rounded-3">

    <div class="card-header bg-white border-bottom px-3 py-2">
      <h6 class="mb-0 fw-semibold">
        <i class="fas fa-plus-circle text-muted me-2"></i>
        Nueva Solicitud
      </h6>
    </div>

    <div class="card-body p-3">
      <form wire:submit.prevent="guardar">
        <div class="row g-2">

          <div class="col-12 position-relative"
            x-data="{ open: false }"
            @click.outside="open = false">

            <label class="form-label small fw-semibold mb-1">
              Empleado <span class="text-danger">*</span>
            </label>

            <input type="text"
              class="form-control form-control-sm rounded-2"
              wire:model.live.debounce.300ms="busquedaEmpleado"
              @focus="open = true"
              @input="open = true"
              placeholder="Buscar por nombre o número...">

            @if(!empty($empleadosFiltrados))

            <div x-show="open"
              x-transition
              class="dropdown-menu show w-100 mt-1 p-0 shadow-sm rounded-2 border-0"
              style="max-height: 220px; overflow-y: auto;">

              @foreach($empleadosFiltrados as $e)

              <button type="button"
                wire:key="empleado-{{ $e['NumeroEmpleado'] }}"
                wire:mousedown.prevent="seleccionarEmpleado('{{ $e['NumeroEmpleado'] }}','{{ $e['Nombre'] }}')"
                @mousedown="open = false"
                class="dropdown-item py-2 px-3 small">

                <div class="d-flex justify-content-between align-items-center">

                  <div>
                    <div class="fw-semibold">
                      {{ $e['NumeroEmpleado'] }}
                    </div>

                    <small class="text-secondary">
                      {{ $e['Nombre'] }}
                    </small>
                  </div>

                  <i class="fas fa-user text-muted small"></i>

                </div>

              </button>

              @endforeach

            </div>

            @endif
          </div>

          <!-- DEPTO + PUESTO -->
          <div class="col-6">
            <label class="form-label small fw-semibold mb-1">Departamento <span class="text-danger">*</span></label>
            <select class="form-select form-select-sm rounded-2 @error('Departamento') is-invalid @enderror"
              wire:model="Departamento">
              <option value="">Selecciona un departamento</option>
              <option value="Ingenieria">Ingeniería</option>
              <option value="Calidad">Calidad</option>
              <option value="Produccion">Producción</option>
              <option value="Cadena de suministros">Cadena de suministros</option>
              <option value="Finanzas">Finanzas</option>
              <option value="RH">RH</option>
              <option value="IT">IT</option>
              <option value="Mantenimiento">Mantenimiento</option>
            </select>
          </div>

          <div class="col-6">
            <label class="form-label small fw-semibold mb-1">Puesto autoriza</label>
            <select class="form-select form-select-sm rounded-2" wire:model="Puesto">
              <option value="">Seleccionar</option>
              <option>Gerente</option>
              <option>Ingeniero</option>
              <option>Supervisor</option>
            </select>
          </div>

          <!-- FECHA + HORA -->
          <!-- FECHA -->
          <div class="col-6">
            <label class="form-label small fw-semibold mb-1">
              Fecha <span class="text-danger">*</span>
            </label>

            <input type="date"
              class="form-control form-control-sm rounded-2"
              wire:model.live="FechaSolicitud">
          </div>

          <!-- HORA INICIO -->
          <div class="col-3">
            <label class="form-label small fw-semibold mb-1">
              Hora inicio
            </label>

            <input type="time"
              class="form-control form-control-sm rounded-2"
              wire:model.live="HoraInicio">
          </div>

          <!-- HORA FIN -->
          <div class="col-3">
            <label class="form-label small fw-semibold mb-1">
              Hora fin
            </label>

            <input type="time"
              class="form-control form-control-sm rounded-2"
              wire:model.live="HoraFin">
          </div>

          <!-- HORAS EXTRA -->
          <div class="col-6">
            <label class="form-label small fw-semibold mb-1">
              Horas extra calculadas
            </label>

            <div class="input-group input-group-sm">

              <input type="text"
                class="form-control rounded-start-2 bg-light fw-semibold text-center"
                value="{{ $HorasExtra ?? 0 }}"
                readonly>

              <span class="input-group-text bg-light">
                hrs
              </span>
            </div>

            <small class="text-muted">
              Redondeo automático aplicado.
            </small>
          </div>

          <!-- DESCRIPCIÓN -->
          <div class="col-12">
            <label class="form-label small fw-semibold mb-1">Descripción <span class="text-danger">*</span></label>
            <textarea class="form-control form-control-sm rounded-2" rows="2" wire:model="Descripcion" placeholder="Motivo..."></textarea>
          </div>

          <!-- CAUSAS -->
          <div class="col-12">
            <label class="form-label small fw-semibold mb-1">Causas <span class="text-danger">*</span></label>
            <textarea class="form-control form-control-sm rounded-2" rows="2" wire:model="Causas" placeholder="Justificación..."></textarea>
          </div>

        </div>

        <button type="submit" class="btn btn-primary btn-sm w-100 rounded-2 mt-3 py-1 fw-semibold">
          <i class="fas fa-save me-1"></i> Guardar
        </button>
      </form>
    </div>

  </div>
</div>