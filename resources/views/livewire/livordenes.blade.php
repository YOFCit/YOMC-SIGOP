<div>
  <div class="container-fluid px-4">
    <div class="row g-4">
      <!-- FORMULARIO - Panel izquierdo -->
      @include('Components.OrdenesComponents.ORV1')
      <!-- TABLA DE ÓRDENES -->
      @include('Components.OrdenesComponents.ORV2')
    </div>
  </div>
  <!-- MODAL PARA VER MOVIMIENTOS -->
  @if($showMovimientosModal && $ordenSeleccionada)
  @include('Components.OrdenesComponents.ORV3')
  @endif
</div>