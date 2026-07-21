<div class="col-xl-4 col-lg-5">
  <div class="sticky-top pt-3">
    <div class="card shadow-sm border-0 rounded-3">
      <div class="card-header bg-white">
        <h5 class="mb-0 fw-semibold">
          <i class="fas fa-sitemap text-primary me-2"></i>
          Catálogo
        </h5>
      </div>

      <div class="accordion accordion-flush" id="catalogoAccordion">

        <!-- ÁREA -->
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#areaCollapse">
              <i class="fas fa-building text-primary me-2"></i>Área
            </button>
          </h2>

          <div id="areaCollapse" class="accordion-collapse collapse show">
            <div class="accordion-body">

              <label class="form-label small">Nombre</label>

              <input class="form-control form-control-sm @error('areaNombre') is-invalid @enderror"
                wire:model.defer="areaNombre">

              @error('areaNombre')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror

              <button class="btn btn-primary btn-sm w-100 mt-3"
                wire:click="guardarArea">

                <i class="fas fa-save me-1"></i>

                {{ $editAreaId ? 'Actualizar' : 'Guardar' }}

              </button>

            </div>
          </div>
        </div>

        <!-- LÍNEA -->

        <div class="accordion-item">

          <h2 class="accordion-header">

            <button class="accordion-button collapsed"
              data-bs-toggle="collapse"
              data-bs-target="#lineaCollapse">

              <i class="fas fa-stream text-success me-2"></i>

              Línea

            </button>

          </h2>

          <div id="lineaCollapse" class="accordion-collapse collapse">

            <div class="accordion-body">

              <label class="form-label small">Área</label>

              <select class="form-select form-select-sm"
                wire:model.live="selectedArea">

                <option value="">Seleccione...</option>

                @foreach($listaAreas as $area)
                <option value="{{ $area->IdArea }}">
                  {{ $area->Nombre }}
                </option>
                @endforeach

              </select>

              <label class="form-label small mt-2">
                Nombre
              </label>

              <input class="form-control form-control-sm"
                wire:model.defer="lineaNombre">

              <button class="btn btn-success btn-sm w-100 mt-3"
                wire:click="guardarLinea">

                <i class="fas fa-save me-1"></i>

                {{ $editLineaId ? 'Actualizar' : 'Guardar' }}

              </button>

            </div>

          </div>

        </div>

        <!-- MÁQUINA -->

        <div class="accordion-item">

          <h2 class="accordion-header">

            <button class="accordion-button collapsed"
              data-bs-toggle="collapse"
              data-bs-target="#maquinaCollapse">

              <i class="fas fa-cogs text-warning me-2"></i>

              Máquina

            </button>

          </h2>

          <div id="maquinaCollapse"
            class="accordion-collapse collapse">

            <div class="accordion-body">

              <label class="form-label small">
                Línea
              </label>

              <select class="form-select form-select-sm"
                wire:model="selectedLinea">

                <option value="">Seleccione...</option>

                @foreach($listaLineas as $linea)

                <option value="{{ $linea->IdLinea }}">
                  {{ $linea->Nombre }}
                </option>

                @endforeach

              </select>

              <label class="form-label small mt-2">
                Nombre
              </label>

              <input class="form-control form-control-sm"
                wire:model.defer="maquinaNombre">

              <button class="btn btn-warning btn-sm text-white w-100 mt-3"
                wire:click="guardarMaquina">

                <i class="fas fa-save me-1"></i>

                {{ $editMaquinaId ? 'Actualizar' : 'Guardar' }}

              </button>

            </div>

          </div>

        </div>

      </div>

      <div class="card-footer bg-white">

        <button class="btn btn-outline-secondary btn-sm w-100"
          wire:click="limpiar">

          <i class="fas fa-eraser me-1"></i>

          Limpiar

        </button>

      </div>

    </div>
  </div>
</div>