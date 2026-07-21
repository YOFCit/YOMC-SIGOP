<div class="col-xl-3 col-lg-4">
  <div style="position: sticky; top: 20px;">
    <div class="card shadow-sm border-0 rounded-3">
      <div class="card-header bg-white border-bottom px-4 py-3 rounded-top-3">
        <div class="d-flex justify-content-between align-items-center">
          <h6 class="mb-0 fw-semibold">
            <i class="fas fa-filter me-2 text-primary"></i>Filtros
          </h6>
          <button class="btn btn-sm btn-primary rounded-pill px-3" wire:click="abrirFormulario">
            <i class="fas fa-plus me-1"></i> Nuevo
          </button>
        </div>
      </div>
      <div class="card-body p-4">
        <div class="mb-3">
          <label class="form-label small fw-semibold mb-2">Buscar</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-end-0">
              <i class="fas fa-search text-muted"></i>
            </span>
            <input type="text" class="form-control form-control-sm border-start-0 ps-0"
              placeholder="Nombre, empleado, descripción..."
              wire:model.live.debounce.300ms="search">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label small fw-semibold mb-2">Estado</label>
          <select class="form-select form-select-sm" wire:model.live="status">
            <option value="">Todos</option>
            <option value="abierto">Abierto</option>
            <option value="cerrado">Cerrado</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label small fw-semibold mb-2">Área</label>
          <select class="form-select form-select-sm" wire:model.live="area">
            <option value="">Todas</option>
            @foreach($areas as $a)
            <option value="{{ $a }}">{{ $a }}</option>
            @endforeach
          </select>
        </div>

        @if($isAdmin)
        <div class="mb-3">
          <label class="form-label small fw-semibold mb-2">Departamento</label>
          <select class="form-select form-select-sm" wire:model.live="departamentoFiltro">
            <option value="">Todos</option>
            @foreach($departamentos as $d)
            <option value="{{ $d }}">{{ $d }}</option>
            @endforeach
          </select>
        </div>
        @endif

        <div class="mb-3">
          <label class="form-label small fw-semibold mb-2">Fecha desde</label>
          <input type="date" class="form-control form-control-sm" wire:model.live="fechaDesde">
        </div>

        <div class="mb-3">
          <label class="form-label small fw-semibold mb-2">Fecha hasta</label>
          <input type="date" class="form-control form-control-sm" wire:model.live="fechaHasta">
        </div>

        <button class="btn btn-sm btn-outline-secondary w-100 mt-2 rounded-pill" wire:click="limpiarFiltros">
          <i class="fas fa-eraser me-1"></i> Limpiar filtros
        </button>
      </div>
    </div>
  </div>
</div>