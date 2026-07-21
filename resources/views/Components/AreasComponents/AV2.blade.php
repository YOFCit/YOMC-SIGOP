<div class="col-xl-8 col-lg-7">

  <div class="card shadow-sm border-0 rounded-3">

    <div class="card-header bg-white border-bottom">
      <div class="d-flex justify-content-between align-items-center">

        <h5 class="mb-0 fw-semibold">
          <i class="fas fa-sitemap text-primary me-2"></i>
          Estructura de Producción
        </h5>
        <span class="badge bg-primary fs-6">
          {{ $areas->total() }} Áreas
        </span>
      </div>
    </div>

    <div class="card-body p-0">
      <div style="max-height:70vh;overflow:auto;">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Estructura</th>
              <th width="220" class="text-center">
                Acciones
              </th>
            </tr>
          </thead>
          <tbody>
            @forelse($areas as $area)
            <!-- AREA -->
            <tr class="table-primary">
              <td>
                <i class="fas fa-building me-2"></i>
                <strong>{{ $area->Nombre }}</strong>
              </td>
              <td class="text-center">
                <button
                  class="btn btn-sm btn-outline-primary"
                  wire:click="editarArea({{ $area->IdArea }})">
                  <i class="fas fa-edit"></i>
                </button>
                <button
                  class="btn btn-sm btn-outline-danger"
                  wire:click="eliminarArea({{ $area->IdArea }})">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
            @forelse($area->lineas as $linea)
            <!-- LINEA -->
            <tr>
              <td class="ps-5">
                <i class="fas fa-level-down-alt text-success me-2"></i>
                <strong>{{ $linea->Nombre }}</strong>
              </td>
              <td class="text-center">
                <button
                  class="btn btn-sm btn-outline-success"
                  wire:click="editarLinea({{ $linea->IdLinea }})">

                  <i class="fas fa-edit"></i>
                </button>
                <button
                  class="btn btn-sm btn-outline-danger"
                  wire:click="eliminarLinea({{ $linea->IdLinea }})">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
            @forelse($linea->maquinas as $maquina)
            <!-- MAQUINA -->
            <tr>
              <td class="ps-7">
                <span style="padding-left:80px">
                  <i class="fas fa-cog text-warning me-2"></i>
                  {{ $maquina->Nombre }}
                </span>
              </td>

              <td class="text-center">
                <button
                  class="btn btn-sm btn-outline-warning"
                  wire:click="editarMaquina({{ $maquina->IdMaquina }})">
                  <i class="fas fa-edit"></i>
                </button>

                <button
                  class="btn btn-sm btn-outline-danger"
                  wire:click="eliminarMaquina({{ $maquina->IdMaquina }})">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
            @empty
            <tr>
              <td class="ps-7 text-muted">
                <span style="padding-left:80px">
                  <i class="fas fa-info-circle me-2"></i>
                  Sin máquinas
                </span>
              </td>
              <td></td>
            </tr>
            @endforelse
            @empty
            <tr>
              <td class="ps-5 text-muted">
                <i class="fas fa-info-circle me-2"></i>
                Sin líneas
              </td>
              <td></td>
            </tr>

            @endforelse
            @empty

            <tr>
              <td colspan="2" class="text-center py-5 text-muted">
                <i class="fas fa-box-open fa-3x mb-3"></i>
                <br>
                No existen registros.
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    @if($areas->hasPages())
    <div class="card-footer">
      {{ $areas->links() }}
    </div>
    @endif
  </div>
</div>