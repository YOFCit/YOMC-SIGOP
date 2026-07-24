<div class="d-flex justify-content-center align-items-center" style="min-height: calc(80vh - 100px);">
  <div class="card shadow-lg border-0" style="width: 420px; border-radius: 20px;">
    <div class="card-body p-4 p-md-5">

      <!-- Header -->
      <div class="text-center mb-4">
        <div class="bg-primary bg-opacity-10 rounded-3 p-3 d-inline-block mb-3">
          <i class="bi bi-ticket-perforated fs-1 text-primary"></i>
        </div>

        <h4 class="fw-bold mb-1 text-dark">
          Sistema de Órdenes
        </h4>
        <p class="text-muted small mb-0 text-uppercase tracking-wide">
          <i class="bi bi-shield-lock me-1"></i> Gestión de tickets
        </p>

        <!-- Divisor -->
        <hr class="w-50 mx-auto mt-3" style="border-top: 2px solid #e9ecef; opacity: 1;">
      </div>

      {{-- ================= LOGIN ================= --}}
      @if ($viewMode == 'login')
      @include('Components.LoginComponents.LV1')
      @endif

      {{-- ================= RESET ================= --}}
      @if ($viewMode == 'reset')
      @include('Components.LoginComponents.LV2')
      @endif

      <!-- Footer -->
      <div class="text-center mt-4 pt-3 border-top">
        <p class="text-muted small mb-0">
          <i class="bi bi-lock-fill me-1"></i>
          Conexión segura
          <span class="mx-2">•</span>
          <i class="bi bi-clock me-1"></i>
          Sesión: <span class="fw-bold">30 min</span>
        </p>
      </div>
    </div>
  </div>
</div>