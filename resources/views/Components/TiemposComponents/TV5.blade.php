<div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5); z-index: 1050;">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4">
      <div class="modal-header bg-light border-0 rounded-top-4">
        <h5 class="modal-title">
          <i class="fas fa-info-circle text-info me-2"></i>
          Detalle de Tiempo Muerto #{{ $detailItem->id }}
        </h5>
        <button type="button" class="btn-close" wire:click="cerrarDetalle"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="border-bottom pb-2">
              <small class="text-muted">Empleado</small>
              <div class="fw-semibold">{{ $detailItem->Name }}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-bottom pb-2">
              <small class="text-muted">No. Empleado</small>
              <div class="fw-semibold">{{ $detailItem->EmployeeID }}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-bottom pb-2">
              <small class="text-muted">Departamento</small>
              <div class="fw-semibold">{{ $detailItem->Departament }}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-bottom pb-2">
              <small class="text-muted">Área</small>
              <div class="fw-semibold">{{ $detailItem->Area }}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-bottom pb-2">
              <small class="text-muted">Línea</small>
              <div class="fw-semibold">{{ $detailItem->ProductionLine }}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-bottom pb-2">
              <small class="text-muted">Tiempo usado</small>
              <div class="fw-semibold">{{ $detailItem->TimeUsed ?? 0 }} minutos</div>
            </div>
          </div>
          <div class="col-12">
            <div class="border-bottom pb-2">
              <small class="text-muted">Descripción del problema</small>
              <div class="fw-semibold">{{ $detailItem->Description }}</div>
            </div>
          </div>
          @if($detailItem->SolutionDescription)
          <div class="col-12">
            <div class="border-bottom pb-2">
              <small class="text-muted text-success">Solución</small>
              <div class="fw-semibold text-success">{{ $detailItem->SolutionDescription }}</div>
            </div>
          </div>
          @endif
          <div class="col-md-6">
            <div class="border-bottom pb-2">
              <small class="text-muted">Fecha apertura</small>
              <div class="fw-semibold">{{ $detailItem->DateOfOpen ? Carbon\Carbon::parse($detailItem->DateOfOpen)->format('d/m/Y H:i:s') : 'N/A' }}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border-bottom pb-2">
              <small class="text-muted">Estado</small>
              <div>
                @if($detailItem->Status === 'cerrado')
                <span class="badge bg-success rounded-pill">Cerrado</span>
                @else
                <span class="badge bg-warning rounded-pill">Abierto</span>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 pb-4">
        <button type="button" class="btn btn-secondary rounded-pill px-4" wire:click="cerrarDetalle">
          Cerrar
        </button>
        @if($detailItem->Status === 'abierto')
        @php
        $canClose = $isAdmin || ($detailItem->Departament === $currentUser->Departamento);
        @endphp
        @if($canClose)
        <button type="button" class="btn btn-success rounded-pill px-4" wire:click="abrirCerrar({{ $detailItem->id }})">
          <i class="fas fa-check-circle me-1"></i> Cerrar
        </button>
        @endif
        @endif
      </div>
    </div>
  </div>
</div>