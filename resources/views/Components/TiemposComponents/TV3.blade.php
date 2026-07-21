<div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5); z-index: 1050;">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4">
      <div class="modal-header bg-light border-0 rounded-top-4">
        <h5 class="modal-title">
          <i class="fas fa-plus-circle text-primary me-2"></i>
          Nuevo Tiempo Muerto
        </h5>
        <button type="button" class="btn-close" wire:click="cerrarFormulario"></button>
      </div>
      <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
        <div class="alert alert-info border-0 rounded-3 mb-4">
          <div class="row g-2 small">
            <div class="col-md-4">
              <i class="fas fa-user me-1"></i>
              <strong>Empleado:</strong> {{ $Name }}
            </div>
            <div class="col-md-4">
              <i class="fas fa-id-badge me-1"></i>
              <strong>No. Empleado:</strong> {{ $EmployeeID }}
            </div>
            @if(!$isAdmin)
            <div class="col-md-4">
              <i class="fas fa-building me-1"></i>
              <strong>Departamento:</strong> {{ $Departament }}
            </div>
            @endif
          </div>
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold small">Área <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('Area') is-invalid @enderror rounded-3"
              wire:model="Area" placeholder="Ej: Producción, Mantenimiento">
            @error('Area') <div class="invalid-feedback small">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold small">Línea de Producción <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('ProductionLine') is-invalid @enderror rounded-3"
              wire:model="ProductionLine" placeholder="Ej: Línea 1, Ensamble">
            @error('ProductionLine') <div class="invalid-feedback small">{{ $message }}</div> @enderror
          </div>

          @if($isAdmin)
          <div class="col-md-6">
            <label class="form-label fw-semibold small">Departamento <span class="text-danger">*</span></label>
            <select class="form-select @error('Departament') is-invalid @enderror rounded-3" wire:model="Departament">
              <option value="">Seleccione</option>
              @foreach($departamentosLista as $dept)
              <option value="{{ $dept }}">{{ $dept }}</option>
              @endforeach
            </select>
            @error('Departament') <div class="invalid-feedback small">{{ $message }}</div> @enderror
          </div>
          @else
          <input type="hidden" wire:model="Departament">
          @endif

          <div class="col-md-6">
            <label class="form-label fw-semibold small">Fecha y Hora <span class="text-danger">*</span></label>
            <input type="datetime-local" class="form-control @error('DateOfOpen') is-invalid @enderror rounded-3"
              wire:model="DateOfOpen">
            @error('DateOfOpen') <div class="invalid-feedback small">{{ $message }}</div> @enderror
          </div>

          <div class="col-12">
            <label class="form-label fw-semibold small">Descripción del problema <span class="text-danger">*</span></label>
            <textarea class="form-control @error('Description') is-invalid @enderror rounded-3"
              rows="3" wire:model="Description"
              placeholder="Describa detalladamente el problema..."></textarea>
            @error('Description') <div class="invalid-feedback small">{{ $message }}</div> @enderror
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 pb-4">
        <button type="button" class="btn btn-secondary rounded-pill px-4" wire:click="cerrarFormulario">
          Cancelar
        </button>
        <button type="button" class="btn btn-primary rounded-pill px-4" wire:click="guardar">
          <i class="fas fa-save me-1"></i> Guardar
        </button>
      </div>
    </div>
  </div>
</div>