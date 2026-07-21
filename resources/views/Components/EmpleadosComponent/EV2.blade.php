<div class="col-xl-8 col-lg-7">
  <div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white border-bottom px-4 py-3 rounded-top-3">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h5 class="mb-0 fw-semibold">
          <i class="fas fa-users me-2 text-primary"></i>Empleados Registrados
        </h5>
        <div class="d-flex gap-2">
          <div class="text-center px-3 py-1 bg-light rounded">
            <div class="small text-muted">Total</div>
            <div class="fw-bold fs-6">{{ $stats['total'] }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="card-body p-0">
      <!-- Buscador -->
      <div class="p-3 border-bottom">
        <div class="input-group">
          <span class="input-group-text bg-white border-end-0">
            <i class="fas fa-search text-muted"></i>
          </span>
          <input type="text" class="form-control border-start-0 ps-0"
            placeholder="Buscar por número, nombre, puesto o departamento..."
            wire:model.live.debounce.300ms="search">
          @if($search)
          <button class="btn btn-outline-secondary" wire:click="limpiarFiltros">
            <i class="fas fa-times"></i>
          </button>
          @endif
        </div>
      </div>

      <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr class="small text-uppercase">
              <th class="ps-4" style="width: 15%"># Empleado</th>
              <th style="width: 22%">Nombre</th>
              <th style="width: 25%">Correo</th>
              <th style="width: 18%">Puesto</th>
              <th style="width: 20%">Departamento</th>
              <th style="width: 15%" class="text-center pe-4">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($empleados as $emp)
            <tr class="border-bottom">

              <td class="ps-4">
                <span class="fw-semibold text-primary">{{ $emp->NumeroEmpleado }}</span>
              </td>
              <td>
                <div class="fw-semibold">{{ $emp->Nombre }}</div>
              </td>

              <td>
                <span class="text-muted">
                  <i class="fas fa-envelope me-1 text-secondary"></i>
                  {{ $emp->Email }}
                </span>
              </td>
              <td>
                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-1">
                  <i class="fas fa-briefcase me-1"></i>{{ $emp->Position }}
                </span>
              </td>
              <td>

                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">
                  <i class="fas fa-building me-1"></i>{{ $emp->Departamento }}
                </span>
              </td>

              <td class="text-center pe-4">
                <div class="btn-group btn-group-sm" role="group">
                  <button class="btn btn-outline-primary"
                    wire:click="editar({{ $emp->NumeroEmpleado }})"
                    title="Editar">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="btn btn-outline-danger"
                    wire:click="confirmarEliminacion({{ $emp->NumeroEmpleado }})"
                    title="Eliminar">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5">
                <div class="d-flex flex-column align-items-center justify-content-center py-5 text-muted">
                  <i class="fas fa-inbox fa-3x mb-3"></i>

                  <p class="mb-0">No hay empleados registrados</p>

                  <button class="btn btn-sm btn-primary mt-3 rounded-pill" wire:click="limpiar">
                    <i class="fas fa-plus me-1"></i> Registrar primer empleado
                  </button>
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Paginación -->
    @if($empleados->hasPages())
    <div class="card-footer bg-white border-top px-4 py-3 rounded-bottom-3">
      {{ $empleados->links() }}
    </div>
    @endif
  </div>
  @if($empleadoAEliminar)
  <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-3">

        <div class="modal-header">
          <h5 class="modal-title">Confirmar eliminación</h5>
          <button type="button" class="btn-close"
            wire:click="$set('empleadoAEliminar', null)"></button>
        </div>

        <div class="modal-body">
          ⚠️ ¿Seguro que deseas eliminar a:<br>
          <strong>{{ $nombreEmpleado }}</strong>?
          <br><br>
          <small class="text-muted">Esta acción no se puede deshacer.</small>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary"
            wire:click="$set('empleadoAEliminar', null)">
            Cancelar
          </button>

          <button class="btn btn-danger"
            wire:click="eliminar">
            Sí, eliminar
          </button>
        </div>

      </div>
    </div>
  </div>
  @endif
</div>