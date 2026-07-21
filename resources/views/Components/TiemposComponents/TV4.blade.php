<div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5); z-index: 1050;">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header bg-success text-white border-0 rounded-top-4">
        <h5 class="modal-title">
          <i class="fas fa-check-circle me-2"></i>
          Cerrar Tiempo Muerto
        </h5>
        <button type="button" class="btn-close btn-close-white" wire:click="cerrarCerrarFormulario"></button>
      </div>
      <div class="modal-body p-4">
        <div class="mb-3">
          <label class="form-label fw-semibold small">
            Descripción de la solución <span class="text-danger">*</span>
          </label>
          <textarea class="form-control rounded-3 @error('SolutionDescription') is-invalid @enderror"
            rows="4" wire:model="SolutionDescription"
            placeholder="Describa cómo se solucionó el problema..."></textarea>
          @error('SolutionDescription') <div class="invalid-feedback small">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold small">
            Tiempo utilizado (minutos) <span class="text-danger">*</span>
          </label>
          <input type="number" class="form-control rounded-3 @error('TimeUsed') is-invalid @enderror"
            wire:model="TimeUsed" min="1" placeholder="Ej: 30">
          @error('TimeUsed') <div class="invalid-feedback small">{{ $message }}</div> @enderror
        </div>

        <div class="alert alert-info small mt-3">
          <i class="fas fa-info-circle me-1"></i>
          Al cerrar, este tiempo muerto se contabilizará en las estadísticas.
        </div>
      </div>
      <div class="modal-footer border-0 pb-4">
        <button type="button" class="btn btn-secondary rounded-pill px-4" wire:click="cerrarCerrarFormulario">
          Cancelar
        </button>
        <button type="button" class="btn btn-success rounded-pill px-4" wire:click="guardarCierre">
          <i class="fas fa-save me-1"></i> Cerrar
        </button>
      </div>
    </div>
  </div>
</div>