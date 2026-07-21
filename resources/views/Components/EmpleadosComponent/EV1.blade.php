<div class="col-xl-4 col-lg-5">
  <div style="position: sticky; top: 20px;">
    <div class="card shadow-sm border-0 rounded-3">
      <div class="card-header bg-white border-bottom px-4 py-3 rounded-top-3">
        <h5 class="mb-0 fw-semibold">
          <i class="fas {{ $editId ? 'fa-edit text-warning' : 'fa-user-plus text-primary' }} me-2"></i>
          {{ $editId ? 'Editar Empleado' : 'Nuevo Empleado' }}
        </h5>
      </div>
      <div class="card-body p-4">
        <div class="mb-3">
          <label class="form-label fw-semibold small">
            Número de Empleado <span class="text-danger">*</span>
          </label>
          <input type="number" class="form-control rounded-3 @error('NumeroEmpleado') is-invalid @enderror"
            wire:model="NumeroEmpleado" placeholder="Ej: 8300109">
          @error('NumeroEmpleado') <div class="invalid-feedback small">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold small">
            Nombre Completo <span class="text-danger">*</span>
          </label>
          <input type="text" class="form-control rounded-3 @error('Nombre') is-invalid @enderror"
            wire:model="Nombre" placeholder="Ej: Juan Pérez">
          @error('Nombre') <div class="invalid-feedback small">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold small">
            Correo Electrónico <span class="text-danger">*</span>
          </label>
          <input
            type="email"
            class="form-control rounded-3 @error('Email') is-invalid @enderror"
            wire:model="Email"
            placeholder="ejemplo@empresa.com">

          @error('Email')
          <div class="invalid-feedback small">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold small">
            Puesto <span class="text-danger">*</span>
          </label>
          <select class="form-select rounded-3 @error('Position') is-invalid @enderror"
            wire:model="Position">
            <option value="">Selecciona un puesto</option>
            <option value="Gerente">Gerente</option>
            <option value="Ingeniero">Ingeniero</option>
            <option value="Supervisor">Supervisor</option>
            <option value="Asistente">Asistente</option>
          </select>
          @error('Position') <div class="invalid-feedback small">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold small">
            Departamento <span class="text-danger">*</span>
          </label>
          <select class="form-select rounded-3 @error('Departamento') is-invalid @enderror"
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
          @error('Departamento') <div class="invalid-feedback small">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold small">
            Contraseña <span class="text-danger">{{ $editId ? '' : '*' }}</span>
          </label>
          <input type="password" class="form-control rounded-3 @error('password') is-invalid @enderror"
            wire:model="password" placeholder="{{ $editId ? 'Dejar en blanco para mantener' : 'Mínimo 4 caracteres' }}">
          @error('password') <div class="invalid-feedback small">{{ $message }}</div> @enderror
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