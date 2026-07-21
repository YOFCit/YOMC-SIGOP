@if($showMovimientosModal && $ordenSeleccionada)
<div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5); z-index: 1050;">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4">
      <div class="modal-header bg-light border-0 rounded-top-4">
        <h5 class="modal-title">
          <i class="fas fa-boxes text-primary me-2"></i>
          Materiales de la Orden {{ $ordenSeleccionada->Folio }}
        </h5>
        <button type="button" class="btn-close" wire:click="cerrarModalMovimientos"></button>
      </div>
      <div class="modal-body p-4">
        @if(count($movimientosOrden) > 0)
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr class="small text-uppercase">
                <th>Material</th>
                <th class="text-center">Cantidad</th>
                <th class="text-center">Tipo</th>
                <th class="text-center">Fecha</th>
              </tr>
            </thead>
            <tbody>
              @foreach($movimientosOrden as $mov)
              <tr class="border-bottom">
                <td>
                  <div class="fw-semibold">{{ $mov['material'] }}</div>
                </td>
                <td class="text-center"><span class="fw-semibold">{{ $mov['cantidad'] }}</span></td>
                <td class="text-center">
                  @if($mov['tipo'] === 'salida')
                  <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1">
                    <i class="fas fa-arrow-up me-1"></i> Salida
                  </span>
                  @else
                  <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">
                    <i class="fas fa-arrow-down me-1"></i> Entrada
                  </span>
                  @endif
                </td>
                <td class="text-center small">{{ $mov['fecha'] }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @else
        <div class="d-flex flex-column align-items-center justify-content-center py-5 text-muted">
          <i class="fas fa-inbox fa-3x mb-3"></i>
          <p class="mb-0">Esta orden no tiene materiales registrados</p>
        </div>
        @endif
      </div>
      <div class="modal-footer border-0 pb-4">
        <button type="button" class="btn btn-secondary rounded-pill px-4" wire:click="cerrarModalMovimientos">Cerrar</button>
      </div>
    </div>
  </div>
</div>
@endif