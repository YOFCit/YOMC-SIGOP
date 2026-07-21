<div class="d-flex justify-content-center align-items-center" style="min-height: calc(80vh - 100px);">
  <div class="card shadow border-0" style="width: 380px; border-radius: 16px;">
    <div class="card-body p-3 p-md-4">
      <div class="text-center mb-4">
        <div class="bg-primary bg-opacity-10 rounded-circle p-2 d-inline-block mb-2">
          <i class="bi bi-ticket-perforated fs-3 text-primary"></i>
        </div>
        <h4 class="fw-bold mb-1">
          Sistema de Órdenes
        </h4>
        <p class="text-muted small mb-0" style="font-size: 0.8rem;">Gestión de tickets</p>
      </div>
      {{-- ================= LOGIN ================= --}}
      @if ($viewMode == 'login')
      @include('Components\loginComponents\LV1')
      @endif

      {{-- ================= RESET ================= --}}
      @if ($viewMode == 'reset')
      @include('Components\loginComponents\LV2')
      @endif
    </div>
  </div>
</div>