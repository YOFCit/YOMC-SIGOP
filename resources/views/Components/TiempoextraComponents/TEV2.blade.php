<div class="col-xl-8 col-lg-7">
  <div class="card border-0 shadow-sm rounded-3">

    <div class="card-header bg-white border-bottom px-4 py-3">
      <div class="d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold">
          <i class="fas fa-clock text-muted me-2"></i>
          Solicitudes de Horas Extra
        </h6>
        <span class="badge bg-light text-dark">
          {{ count($registros) }} registros
        </span>
      </div>
    </div>

    <!-- Tabla -->
    <div class="table-responsive" style="max-height: 500px;">
      <table class="table table-hover align-middle mb-0">

        <thead class="bg-light border-bottom">
          <tr class="small text-secondary">
            <th class="px-4 py-3 fw-semibold">Empleado</th>
            <th class="py-3 fw-semibold">Departamento</th>
            <th class="py-3 fw-semibold">Fecha</th>
            <th class="py-3 fw-semibold">Horas</th>
            <th class="py-3 fw-semibold">Descripción</th>
            <th class="py-3 fw-semibold">Estatus</th>
            @if(auth()->check())
            <th class="text-center py-3 fw-semibold">Acciones</th>
            @endif
          </tr>
        </thead>

        <tbody>
          @forelse($registros as $r)
          <tr class="border-bottom">

            <td class="px-4 py-3">
              <div>
                <div class="fw-semibold">{{ $r->Nombre }}</div>
                <small class="text-secondary">{{ $r->NumeroEmpleado }}</small>
              </div>
            </td>

            <td class="py-3">
              <span>{{ $r->Departamento }}</span>
            </td>

            <td class="py-3">
              {{ \Carbon\Carbon::parse($r->FechaSolicitud)->format('d/m/Y') }}
            </td>

            <td class="py-3">
              <div class="fw-semibold">
                {{ $r->HorasExtra ?? 0 }} hrs
              </div>

              <small class="text-secondary">
                {{ $r->HoraInicio }}
                -
                {{ $r->HoraFin }}
              </small>
            </td>

            <td class="py-3">
              <div class="text-truncate" style="max-width: 200px;" title="{{ $r->Descripcion }}">
                {{ \Str::limit($r->Descripcion, 50) }}
              </div>
            </td>

            <td class="py-3">
              @if($r->Estatus == 'Pendiente')
              <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Pendiente</span>
              @elseif($r->Estatus == 'Autorizado')
              <span class="badge bg-success px-3 py-2 rounded-pill">Autorizado</span>
              @else
              <span class="badge bg-danger px-3 py-2 rounded-pill">Rechazado</span>
              @endif
            </td>

            @if(auth()->check())
            <td class="py-3">
              <div class="d-flex gap-2 justify-content-center">
                @if(( ($this->esJefe()&& $this->esMismoDepartamento($r->Departamento)) || $this->esAdmin()) && $r->Estatus == 'Pendiente')
                <button class="btn btn-success btn-sm px-3 py-1 rounded-2"
                  wire:click="autorizar({{ $r->id }})"
                  title="Autorizar">
                  <i class="fas fa-check me-1"></i> Autorizar
                </button>
                <button class="btn btn-danger btn-sm px-3 py-1 rounded-2"
                  wire:click="rechazar({{ $r->id }})"
                  title="Rechazar">
                  <i class="fas fa-times me-1"></i> Rechazar
                </button>
                @endif

                @if($r->Estatus == 'Autorizado')
                <button class="btn btn-outline-secondary btn-sm px-3 py-1 rounded-2"
                  wire:click="exportarWord({{ $r->id }})"
                  title="Exportar a Word">
                  <i class="fas fa-file-word me-1"></i> Word
                </button>
                @endif
              </div>
            </td>
            @endif

          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center py-5">
              <div class="d-flex flex-column align-items-center">
                <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                <p class="mb-0">No hay solicitudes registradas</p>
                <small>Complete el formulario para crear una nueva solicitud</small>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>

      </table>
    </div>

  </div>
</div>