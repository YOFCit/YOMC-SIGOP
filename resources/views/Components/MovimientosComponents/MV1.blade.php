<div class="col-xl-3 col-lg-4">
  <div style="position: sticky; top: 20px;">
    <div class="card shadow-sm border-0 rounded-3">
      <div class="card-header bg-white border-bottom px-4 py-3 rounded-top-3">
        <h6 class="mb-0 fw-semibold">
          <i class="fas fa-filter me-2 text-primary"></i>Filtros
        </h6>
      </div>
      <div class="card-body p-4">
        <div class="mb-3">
          <label class="form-label small fw-semibold mb-2">Buscar</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-end-0">
              <i class="fas fa-search text-muted"></i>
            </span>
            <input type="text" class="form-control form-control-sm border-start-0 ps-0"
              placeholder="Material u orden..."
              wire:model.live.debounce.300ms="search">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label small fw-semibold mb-2">Tipo</label>
          <select class="form-select form-select-sm" wire:model.live="tipoMovimiento">
            <option value="">Todos</option>
            <option value="entrada">Entrada</option>
            <option value="salida">Salida</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label small fw-semibold mb-2">Material</label>
          <select class="form-select form-select-sm" wire:model.live="materialId">
            <option value="">Todos</option>
            @foreach($materiales as $m)
            <option value="{{ $m->IdMaterial }}">{{ $m->Nombre }}</option>
            @endforeach
          </select>
        </div>

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