      <form wire:submit.prevent="resetPassword">
        <div class="mb-3">
          <label class="form-label fw-semibold small text-secondary">
            <i class="bi bi-person-badge me-1"></i>
            Número de Empleado
          </label>
          <input
            type="number"
            class="form-control form-control-lg"
            style="border-radius: 10px;"
            wire:model="resetEmpleado"
            placeholder="Ingresa tu número">
          @error('resetEmpleado')
          <small class="text-danger d-block mt-1">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            {{ $message }}
          </small>
          @enderror
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold small text-secondary">
            <i class="bi bi-key me-1"></i>
            Nueva Contraseña
          </label>
          <input
            type="password"
            class="form-control form-control-lg"
            style="border-radius: 10px;"
            wire:model="newPassword"
            placeholder="Mínimo 6 caracteres">
          @error('newPassword')
          <small class="text-danger d-block mt-1">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            {{ $message }}
          </small>
          @enderror
        </div>

        <div class="mb-4">
          <label class="form-label fw-semibold small text-secondary">
            <i class="bi bi-check-circle me-1"></i>
            Confirmar Contraseña
          </label>
          <input
            type="password"
            class="form-control form-control-lg"
            style="border-radius: 10px;"
            wire:model="confirmPassword"
            placeholder="Repite tu contraseña">
          @error('confirmPassword')
          <small class="text-danger d-block mt-1">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            {{ $message }}
          </small>
          @enderror
        </div>

        <button class="btn btn-success w-100 py-2 fw-semibold" style="border-radius: 10px; font-size: 1rem;">
          <i class="bi bi-arrow-repeat me-2"></i>
          Restablecer Contraseña
        </button>
      </form>
      <div class="text-center mt-3">
        <button class="btn btn-link text-decoration-none small p-0" wire:click="showLogin">
          <i class="bi bi-arrow-left me-1"></i>
          Volver al Login
        </button>
      </div>