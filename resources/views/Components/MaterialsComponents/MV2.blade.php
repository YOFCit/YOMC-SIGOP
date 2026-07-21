<div class="col-xl-8 col-lg-7">
  <div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white border-bottom px-4 py-3 rounded-top-3">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h5 class="mb-0 fw-semibold">
          <i class="fas fa-cubes me-2 text-primary"></i>Inventario de Materiales
        </h5>
        <div class="d-flex gap-2">
          <div class="text-center px-3 py-1 bg-light rounded">
            <div class="small text-muted">Total</div>
            <div class="fw-bold fs-6">{{ $stats['total'] }}</div>
          </div>
          <div class="text-center px-3 py-1 bg-primary bg-opacity-10 rounded">
            <div class="small text-primary">Stock Total</div>
            <div class="fw-bold fs-6 text-primary">{{ number_format($stats['stock_total']) }}</div>
          </div>
          @if($stats['bajo_stock'] > 0)
          <div class="text-center px-3 py-1 bg-warning bg-opacity-10 rounded">
            <div class="small text-warning">Bajo Stock</div>
            <div class="fw-bold fs-6 text-warning">{{ $stats['bajo_stock'] }}</div>
          </div>
          @endif
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
            placeholder="Buscar por nombre, descripción o ubicación..."
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
              <th class="ps-4" style="width: 8%">ID</th>
              <th style="width: 20%">Material</th>
              <th style="width: 25%">Descripción</th>
              <th style="width: 15%">Ubicación</th>
              <th style="width: 12%" class="text-center">Stock</th>
              @if(auth()->check() && in_array(auth()->user()->Position, ['Ingeniero', 'Administrador']))<th style="width: 20%" class="text-center pe-4">Acciones</th>@endif
            </tr>
          </thead>
          <tbody>
            @forelse($materiales as $mat)
            <tr class="border-bottom">
              <td class="ps-4">
                <span class="fw-semibold text-primary">#{{ $mat->IdMaterial }}</span>
              </td>
              <td>
                <div class="fw-semibold">{{ $mat->Nombre }}</div>
              </td>
              <td>
                <div class="small">{{ Str::limit($mat->Descripcion, 50) }}</div>
              </td>
              <td>
                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-1">
                  <i class="fas fa-map-marker-alt me-1"></i>{{ $mat->Location }}
                </span>
              </td>
              <td class="text-center">
                @if($mat->Stock <= 5)
                  <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1">
                  <i class="fas fa-exclamation-triangle me-1"></i>{{ $mat->Stock }}
                  </span>
                  @else
                  <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">
                    <i class="fas fa-box me-1"></i>{{ $mat->Stock }}
                  </span>
                  @endif
              </td>
              @if(auth()->check() && in_array(auth()->user()->Position, ['Ingeniero', 'Administrador']))
              <td class="text-center pe-4">
                <div class="d-flex gap-1 justify-content-center">
                  <button class="btn-icon btn-icon-edit"
                    wire:click="editar({{ $mat->IdMaterial }})"
                    title="Editar material">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="btn-icon btn-icon-delete"
                    wire:click="confirmarEliminacion({{ $mat->IdMaterial }})"
                    title="Eliminar material">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </div>
              </td>
              @endif
            </tr>
            @empty
            <tr>
              <td colspan="6">
                <div class="d-flex flex-column align-items-center justify-content-center py-5 text-muted">
                  <i class="fas fa-inbox fa-3x mb-3"></i>

                  <p class="mb-0">No hay materiales registrados</p>

                  <button class="btn btn-sm btn-primary mt-3 rounded-pill" wire:click="limpiar">
                    <i class="fas fa-plus me-1"></i> Registrar primer material
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
    @if($materiales->hasPages())
    <div class="card-footer bg-white border-top px-4 py-3 rounded-bottom-3">
      {{ $materiales->links() }}
    </div>
    @endif
  </div>


  @if($materialAEliminar)
  <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-3">

        <div class="modal-header">
          <h5 class="modal-title">Confirmar eliminación</h5>
          <button type="button" class="btn-close" wire:click="$set('materialAEliminar', null)"></button>
        </div>

        <div class="modal-body">
          ⚠️ ¿Seguro que deseas eliminar este material "{{ $nombreMaterial }}"?<br>
          <small class="text-muted">Esta acción no se puede deshacer.</small>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary"
            wire:click="$set('materialAEliminar', null)">
            Cancelar
          </button>

          <button class="btn btn-danger"
            wire:click="eliminar({{ $materialAEliminar }})">
            Sí, eliminar
          </button>
        </div>

      </div>
    </div>
  </div>
  @endif
</div>