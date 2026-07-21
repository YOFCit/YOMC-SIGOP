<!-- Panel de formulario -->
<div class="col-xl-4 col-lg-5">
  <div style="position: sticky; top: 20px;">
    <div class="card shadow-sm border-0 rounded-3">
      <div class="card-header bg-white border-bottom px-4 py-3 rounded-top-3">
        <h5 class="mb-0 fw-semibold">
          <i class="fas {{ $editId ? 'fa-edit text-warning' : 'fa-boxes text-primary' }} me-2"></i>
          {{ $editId ? 'Editar Material' : 'Nuevo Material' }}
        </h5>
      </div>
      <div class="card-body p-4">
        <div class="mb-3">
          <label class="form-label fw-semibold small">
            Nombre del Material <span class="text-danger">*</span>
          </label>
          <input type="text" class="form-control rounded-3 @error('Nombre') is-invalid @enderror"
            wire:model="Nombre" placeholder="Ej: Cable UTP, Conector RJ45">
          @error('Nombre') <div class="invalid-feedback small">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold small">
            Descripción <span class="text-danger">*</span>
          </label>
          <textarea class="form-control rounded-3 @error('Descripcion') is-invalid @enderror"
            rows="2" wire:model="Descripcion"
            placeholder="Descripción detallada del material..."></textarea>
          @error('Descripcion') <div class="invalid-feedback small">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold small">
            Ubicación <span class="text-danger">*</span>
          </label>
          <input type="text" class="form-control rounded-3 @error('Location') is-invalid @enderror"
            wire:model="Location" placeholder="Ej: Bodega A, Estante 3">
          @error('Location') <div class="invalid-feedback small">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold small">
            Stock Disponible <span class="text-danger">*</span>
          </label>
          <input type="number" class="form-control rounded-3 @error('Stock') is-invalid @enderror"
            wire:model="Stock" min="0" placeholder="0">
          @error('Stock') <div class="invalid-feedback small">{{ $message }}</div> @enderror
        </div>

        <button class="btn btn-primary w-100 rounded-pill py-2 mb-2" wire:click="guardar">
          <i class="fas fa-save me-1"></i> {{ $editId ? 'Actualizar' : 'Guardar' }}
        </button>

        @if($editId)
        <button class="btn btn-secondary w-100 rounded-pill py-2" wire:click="cancelar">
          <i class="fas fa-times me-1"></i> Cancelar
        </button>
        @else
        <button class="btn btn-outline-secondary w-100 rounded-pill py-2" wire:click="limpiar">
          <i class="fas fa-eraser me-1"></i> Limpiar
        </button>
        @endif
      </div>
    </div>
  </div>
</div>