<div class="col-xl-9 col-lg-8">
  <div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white border-bottom px-4 py-3 rounded-top-3">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h5 class="mb-0 fw-semibold">
          <i class="fas fa-exchange-alt text-primary me-2"></i>Movimientos de Materiales
        </h5>

        <div class="d-flex gap-3">
          <div class="text-center px-3 py-1 bg-light rounded">
            <div class="small text-muted">Total</div>
            <div class="fw-bold fs-6">{{ $stats['total'] }}</div>
          </div>
          <div class="text-center px-3 py-1 bg-success bg-opacity-10 rounded">
            <div class="small text-success">Entradas</div>
            <div class="fw-bold fs-6 text-success">{{ $stats['entradas'] }}</div>
          </div>
          <div class="text-center px-3 py-1 bg-danger bg-opacity-10 rounded">
            <div class="small text-danger">Salidas</div>
            <div class="fw-bold fs-6 text-danger">{{ $stats['salidas'] }}</div>
          </div>
          <div class="text-center px-3 py-1 bg-secondary bg-opacity-10 rounded">
            <div class="small text-secondary">Sin orden</div>
            <div class="fw-bold fs-6 text-secondary">{{ $stats['sin_orden'] }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive" style="max-height: 65vh; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr class="small text-uppercase">
              <th class="ps-4" style="width: 10%">ID</th>
              <th style="width: 25%">Material</th>
              <th style="width: 10%" class="text-center">Cantidad</th>
              <th style="width: 12%" class="text-center">Tipo</th>
              <th style="width: 20%">Orden</th>
              <th style="width: 23%" class="text-center pe-4">Fecha</th>
            </tr>
          </thead>
          <tbody>
            @forelse($movimientos as $mov)
            <tr class="border-bottom">
              <td class="ps-4">
                <span class="fw-semibold text-primary">#{{ $mov->IdMovimiento }}</span>
              </td>
              <td>
                <div class="fw-semibold">{{ $mov->material->Nombre ?? 'N/A' }}</div>
                <div class="small text-muted">{{ $mov->material->Location ?? '' }}</div>
              </td>
              <td class="text-center">
                <span class="fw-semibold">{{ $mov->CantidadUsada }}</span>
              </td>
              <td class="text-center">
                @if($mov->TipoMovimiento === 'entrada')
                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">
                  <i class="fas fa-arrow-down me-1"></i> Entrada
                </span>
                @else
                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1">
                  <i class="fas fa-arrow-up me-1"></i> Salida
                </span>
                @endif
              </td>
              <td>
                @if($mov->orden)
                <div class="d-flex align-items-center gap-1">
                  <button class="btn-icon btn-icon-view"
                    wire:click="verOrden({{ $mov->IdOrden }})"
                    title="Ver orden">
                    <i class="fas fa-clipboard-list"></i>
                  </button>
                  <span class="small">{{ $mov->orden->Folio ?? 'N/A' }}</span>
                </div>
                @else
                <span class="text-muted">
                  <i class="fas fa-box-open me-1"></i> Sin orden
                </span>
                @endif
              </td>
              <td class="text-center pe-4">
                <div class="small">
                  <i class="far fa-calendar-alt me-1 text-muted"></i>
                  {{ $mov->created_at ? $mov->created_at->format('d/m/Y') : 'N/A' }}
                </div>
                <div class="small text-muted">
                  <i class="far fa-clock me-1"></i>
                  {{ $mov->created_at ? $mov->created_at->format('H:i:s') : '' }}
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6">
                <div class="d-flex flex-column align-items-center justify-content-center py-5 text-muted">
                  <i class="fas fa-inbox fa-3x mb-3"></i>
                  <p class="mb-0">No hay movimientos registrados</p>
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Paginación -->
      @if($movimientos->hasPages())
      <div class="card-footer bg-white border-top px-4 py-3 rounded-bottom-3">
        {{ $movimientos->links() }}
      </div>
      @endif
    </div>
  </div>
</div>