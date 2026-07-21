<div>
  <div class="container-fluid px-4">
    <div class="row g-4">
      <!-- Panel de filtros -->
      @include('Components\TiemposComponents\TV1')
      <!-- Tabla de tiempos muertos -->
      @include('Components\TiemposComponents\TV2')
    </div>
  </div>
  <!-- MODAL PARA CREAR NUEVO REGISTRO -->
  @if($showForm)
  @include('Components\TiemposComponents\TV3')
  @endif
  <!-- MODAL PARA CERRAR TIEMPO MUERTO -->
  @if($showCloseForm)
  @include('Components\TiemposComponents\TV4')
  @endif
  <!-- MODAL PARA VER DETALLE -->
  @if($showDetail && $detailItem)
  @include('Components\TiemposComponents\TV5')
  @endif
</div>