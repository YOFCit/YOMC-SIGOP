<div class="container-fluid">
  <div class="row">
    <div class="col-lg-4">
      <div class="card shadow-sm">
        <div class="card-header fw-bold">
          Agregar empleado
        </div>

        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">
              Número de empleado
            </label>
            <input
              type="text"
              class="form-control"
              wire:model.defer="NumeroEmpleado">
            @error('NumeroEmpleado')
            <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">
              Nombre
            </label>
            <input
              type="text"
              class="form-control"
              wire:model.defer="Nombre">
            @error('Nombre')
            <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>
          <button
            class="btn btn-primary w-100"
            wire:click="guardar">
            <i class="fas fa-save"></i>
            Agregar empleado
          </button>
        </div>
      </div>

      <div class="card shadow-sm mt-4">
        <div class="card-header fw-bold">
          Importar empleados
        </div>
        <div class="card-body">
          <input
            type="file"
            class="form-control mb-3"
            wire:model="archivo"
            accept=".xlsx,.xls,.csv">
          <div class="d-grid gap-2">
            <button
              class="btn btn-success"
              wire:click="importar">
              <i class="fas fa-file-import"></i>
              Importar
            </button>
            <button
              class="btn btn-outline-primary"
              wire:click="descargarPlantilla">
              <i class="fas fa-download"></i>
              Descargar plantilla
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-header fw-bold">
          Empleados registrados
        </div>
        <div class="card-body p-0">
          <table class="table table-hover table-striped mb-0">
            <thead>
              <tr>
                <th width="150">No. Empleado</th>
                <th>Nombre</th>
                <th width="90"></th>
              </tr>
            </thead>

            <tbody>
              @forelse($empleados as $empleado)
              <tr>
                <td>
                  {{ $empleado->NumeroEmpleado }}
                </td>
                <td>
                  {{ $empleado->Nombre }}
                </td>
                <td class="text-center">
                  <button
                    class="btn btn-danger btn-sm"
                    wire:click="eliminar({{ $empleado->id }})">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="3" class="text-center py-4">
                  No hay empleados registrados.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="card-footer">
          {{ $empleados->links() }}
        </div>
      </div>
    </div>
  </div>
</div>