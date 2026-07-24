{{-- ============================================ --}}
{{-- MODAL DE MOVIMIENTOS DE MATERIALES --}}
{{-- ============================================ --}}
@if($showMovimientosModal && $ordenSeleccionada)
<div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5); z-index: 1050;">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4 shadow-lg">

      {{-- Encabezado --}}
      <div class="modal-header bg-light border-0 rounded-top-4 px-4 py-3">
        <h5 class="modal-title fw-semibold">
          <i class="fas fa-boxes text-primary me-2"></i>
          Materiales Utilizados
        </h5>
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-primary rounded-pill px-3 py-2">
            <i class="fas fa-file-alt me-1"></i>
            {{ $ordenSeleccionada->Folio }}
          </span>
          <button type="button" class="btn-close" wire:click="cerrarModalMovimientos" aria-label="Cerrar"></button>
        </div>
      </div>

      {{-- Cuerpo --}}
      <div class="modal-body p-4">
        @if(count($movimientosOrden) > 0)
        <div class="table-responsive rounded-3">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr class="small text-uppercase text-muted">
                <th class="ps-3">
                  <i class="fas fa-cube me-1"></i>Material
                </th>
                <th class="text-center">
                  <i class="fas fa-hashtag me-1"></i>Cantidad
                </th>
                <th class="text-center">
                  <i class="fas fa-exchange-alt me-1"></i>Tipo
                </th>
                <th class="text-center pe-3">
                  <i class="far fa-calendar me-1"></i>Fecha
                </th>
              </tr>
            </thead>
            <tbody>
              @foreach($movimientosOrden as $mov)
              <tr class="border-bottom">
                {{-- Material --}}
                <td class="ps-3 py-3">
                  <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-2"
                      style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                      <i class="fas fa-box text-primary small"></i>
                    </div>
                    <span class="fw-semibold">{{ $mov['material'] }}</span>
                  </div>
                </td>

                {{-- Cantidad --}}
                <td class="text-center py-3">
                  <span class="badge bg-light text-dark rounded-pill px-3 py-2 fw-semibold">
                    {{ $mov['cantidad'] }}
                  </span>
                </td>

                {{-- Tipo de movimiento --}}
                <td class="text-center py-3">
                  @if($mov['tipo'] === 'salida')
                  <span class="badge rounded-pill px-3 py-2"
                    style="background-color: #fee2e2; color: #991b1b; font-weight: 500;">
                    <i class="fas fa-arrow-up me-1"></i>Salida
                  </span>
                  @else
                  <span class="badge rounded-pill px-3 py-2"
                    style="background-color: #d1fae5; color: #065f46; font-weight: 500;">
                    <i class="fas fa-arrow-down me-1"></i>Entrada
                  </span>
                  @endif
                </td>

                {{-- Fecha --}}
                <td class="text-center pe-3 py-3">
                  <span class="text-muted small">
                    <i class="far fa-clock me-1"></i>
                    {{ $mov['fecha'] }}
                  </span>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {{-- Resumen rápido --}}
        <div class="mt-3 text-end">
          <small class="text-muted">
            <i class="fas fa-info-circle me-1"></i>
            Total de materiales: <strong>{{ count($movimientosOrden) }}</strong>
          </small>
        </div>
        @else
        <div class="text-center py-5">
          <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3"
            style="width: 80px; height: 80px;">
            <i class="fas fa-box-open fa-2x text-muted"></i>
          </div>
          <h6 class="text-muted mb-1">Sin materiales registrados</h6>
          <p class="text-muted small mb-0">Esta orden no tiene movimientos de materiales</p>
        </div>
        @endif
      </div>

      {{-- Pie --}}
      <div class="modal-footer border-0 pt-0 pb-4 px-4">
        <button type="button"
          class="btn btn-secondary rounded-pill px-4 py-2"
          wire:click="cerrarModalMovimientos">
          <i class="fas fa-times me-2"></i>Cerrar
        </button>
      </div>

    </div>
  </div>
</div>

{{-- Overlay para cerrar al hacer clic fuera --}}
<div class="modal-backdrop fade show" style="z-index: 1040;" wire:click="cerrarModalMovimientos"></div>
@endif