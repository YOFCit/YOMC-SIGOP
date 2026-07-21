<div class="col-xl-9 col-lg-8">
  <div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white border-bottom px-4 py-3 rounded-top-3">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h5 class="mb-0 fw-semibold">
          <i class="fas fa-hourglass-half text-warning me-2"></i>Tiempos Muertos
        </h5>

        <div class="d-flex gap-3">
          <div class="text-center px-3 py-1 bg-light rounded">
            <div class="small text-muted">Total</div>
            <div class="fw-bold fs-6">{{ $stats['total'] }}</div>
          </div>
          <div class="text-center px-3 py-1 bg-warning bg-opacity-10 rounded">
            <div class="small text-warning">Abiertos</div>
            <div class="fw-bold fs-6 text-warning">{{ $stats['abiertos'] }}</div>
          </div>
          <div class="text-center px-3 py-1 bg-success bg-opacity-10 rounded">
            <div class="small text-success">Cerrados</div>
            <div class="fw-bold fs-6 text-success">{{ $stats['cerrados'] }}</div>
          </div>
          <div class="text-center px-3 py-1 bg-primary bg-opacity-10 rounded">
            <div class="small text-primary">Total min</div>
            <div class="fw-bold fs-6 text-primary">{{ number_format($stats['tiempo_total']) }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive" style="max-height: 65vh; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr class="small text-uppercase">
              <th class="ps-4" style="width: 8%">ID</th>
              <th style="width: 18%">Empleado</th>
              <th style="width: 18%">Área / Línea</th>
              <th style="width: 25%">Descripción</th>
              <th style="width: 8%" class="text-center">Tiempo</th>
              <th style="width: 10%" class="text-center">Estado</th>
              <th style="width: 13%" class="text-center pe-4">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($tiemposMuertos as $tm)
            <tr class="border-bottom">
              <td class="ps-4" wire:click="verDetalle({{ $tm->id }})" style="cursor: pointer;">
                <span class="fw-semibold text-primary">#{{ $tm->id }}</span>
              </td>
              <td wire:click="verDetalle({{ $tm->id }})" style="cursor: pointer;">
                <div class="fw-semibold">{{ $tm->Name ?? 'N/A' }}</div>
                <div class="small text-muted">
                  <i class="fas fa-id-card me-1"></i>{{ $tm->EmployeeID ?? 'N/A' }}
                </div>
              </td>
              <td wire:click="verDetalle({{ $tm->id }})" style="cursor: pointer;">
                <div><i class="fas fa-building me-1 text-muted"></i>{{ $tm->Area ?? 'N/A' }}</div>
                <div class="small text-muted">{{ $tm->ProductionLine ?? 'N/A' }}</div>
                <div class="small text-muted">{{ $tm->Departament ?? 'N/A' }}</div>
              </td>
              <td wire:click="verDetalle({{ $tm->id }})" style="cursor: pointer;">
                <div class="text-truncate" style="max-width: 250px;" title="{{ $tm->Description }}">
                  {{ Str::limit($tm->Description, 50) }}
                </div>
                @if($tm->SolutionDescription)
                <div class="small text-success mt-1" title="{{ $tm->SolutionDescription }}">
                  <i class="fas fa-check-circle me-1"></i>
                  {{ Str::limit($tm->SolutionDescription, 35) }}
                </div>
                @endif
              </td>
              <td class="text-center" wire:click="verDetalle({{ $tm->id }})" style="cursor: pointer;">
                <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-pill">
                  <i class="fas fa-clock me-1"></i>
                  {{ $tm->TimeUsed ?? 0 }} min
                </span>
              </td>
              <td class="text-center" wire:click="verDetalle({{ $tm->id }})" style="cursor: pointer;">
                @if($tm->Status === 'cerrado')
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-1 rounded-pill">
                  <i class="fas fa-check-circle me-1"></i> Cerrado
                </span>
                @else
                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-1 rounded-pill">
                  <i class="fas fa-play me-1"></i> Abierto
                </span>
                @endif
              </td>
              <td class="text-center pe-4">
                @if($tm->Status === 'abierto')
                @php
                $canClose = $isAdmin || ($tm->Departament === $currentUser->Departamento);
                @endphp
                @if($canClose)
                <button class="btn btn-sm btn-success rounded-pill px-3 w-100"
                  wire:click="abrirCerrar({{ $tm->id }})">
                  <i class="fas fa-check-circle me-1"></i> Cerrar
                </button>
                @else
                <button class="btn btn-sm btn-secondary rounded-pill px-3 w-100" disabled>
                  <i class="fas fa-lock me-1"></i> No autorizado
                </button>
                @endif
                @else
                <span class="badge bg-success bg-opacity-10 text-success rounded-pill py-2 w-100">
                  <i class="fas fa-check me-1"></i> Cerrado
                </span>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                <p class="text-muted mb-0">No hay tiempos muertos registrados</p>
                <button class="btn btn-sm btn-primary mt-3 rounded-pill" wire:click="abrirFormulario">
                  <i class="fas fa-plus me-1"></i> Registrar primero
                </button>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($tiemposMuertos->hasPages())
      <div class="p-3 border-top">
        {{ $tiemposMuertos->links() }}
      </div>
      @endif
    </div>
  </div>
</div>