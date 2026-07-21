@php
switch ($type) {
case 'success':
$borderColor = #28a745;
$icon = 'fas fa-check-circle text-success';
break;
case 'error':
$borderColor = #dc3545;
$icon = 'fas fa-exclamation-circle text-danger';
break;
case 'warning':
$borderColor = #ffc107;
$icon = 'fas fa-exclamation-triangle text-warning';
break;
default:
$borderColor = #17a2b8;
$icon = 'fas fa-info-circle text-info';
}
@endphp

<div>
  @if($show)
  <div class="position-fixed top-0 end-0 m-3" style="z-index: 9999; min-width: 320px;">

    <div class="alert alert-{{ $type }} shadow-lg border-0 rounded-3 d-flex align-items-center justify-content-between"
      role="alert"
      style="border-left: 4px solid '{{ $borderColor }}'; animation: slideInRight 0.3s ease-out;">

      <div class="d-flex align-items-center">
        <div class="me-3">
          <i class="{{ $icon }} fa-lg"></i>
        </div>

        <div>
          <strong class="me-2"></strong>
          <span>{{ $message }}</span>
        </div>
      </div>

      <button type="button" class="btn-close ms-3" wire:click="hideAlert" aria-label="Close"></button>
    </div>

    <style>
      @keyframes slideInRight {
        from {
          transform: translateX(100%);
          opacity: 0;
        }

        to {
          transform: translateX(0);
          opacity: 1;
        }
      }
    </style>

  </div>
  @endif
</div>