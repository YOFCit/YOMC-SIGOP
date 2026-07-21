      <form wire:submit.prevent="login">
        <div class="mb-3">
          <label class="form-label fw-semibold small text-secondary">
            <i class="bi bi-person-badge me-1"></i>
            Número de Empleado
          </label>
          <input
            type="number"
            class="form-control"
            style="border-radius: 10px;"
            wire:model="NumeroEmpleado"
            placeholder="Ej: 12345">
          @error('NumeroEmpleado')
          <small class="text-danger d-block mt-1">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            {{ $message }}
          </small>
          @enderror
        </div>

        <div class="mb-4">
          <label class="form-label fw-semibold text-secondary" style="font-size: 0.8rem;">
            <i class="bi bi-lock me-1"></i>
            Contraseña
          </label>
          <input
            type="password"
            class="form-control form-control-lg"
            style="border-radius: 10px;"
            wire:model="password"
            placeholder="••••••">
          @error('password')
          <small class="text-danger d-block mt-1">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            {{ $message }}
          </small>
          @enderror
        </div>

        <button class="btn btn-primary w-100 py-2 fw-semibold" style="border-radius: 8px; font-size: 0.9rem;">
          <i class="bi bi-box-arrow-in-right me-2"></i>
          Iniciar Sesión
        </button>
      </form>