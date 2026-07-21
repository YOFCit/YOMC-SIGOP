<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Movimiento;
use App\Models\Material;
use Carbon\Carbon;
use Livewire\WithPagination;

class Livmovimientos extends Component
{
  use WithPagination;
  protected $paginationTheme = 'bootstrap';

  public $search = '';
  public $tipoMovimiento = '';
  public $fechaDesde = '';
  public $fechaHasta = '';
  public $materialId = '';

  public function updatingSearch()
  {
    $this->resetPage();
  }

  public function render()
  {
    $query = Movimiento::with(['material', 'orden']);

    // Filtro de búsqueda
    if ($this->search) {
      $query->where(function ($q) {
        $q->whereHas('material', function ($sub) {
          $sub->where('Nombre', 'LIKE', "%{$this->search}%");
        })->orWhereHas('orden', function ($sub) {
          $sub->where('Folio', 'LIKE', "%{$this->search}%");
        });
      });
    }

    // Filtro por tipo de movimiento
    if ($this->tipoMovimiento) {
      $query->where('TipoMovimiento', $this->tipoMovimiento);
    }

    // Filtro por material
    if ($this->materialId) {
      $query->where('IdMaterial', $this->materialId);
    }

    // Filtro por fechas
    if ($this->fechaDesde) {
      $query->whereDate('created_at', '>=', Carbon::parse($this->fechaDesde));
    }

    if ($this->fechaHasta) {
      $query->whereDate('created_at', '<=', Carbon::parse($this->fechaHasta));
    }

    $movimientos = $query->orderBy('created_at', 'desc')->paginate(10);

    // Estadísticas
    $stats = [
      'total' => Movimiento::count(),
      'entradas' => Movimiento::where('TipoMovimiento', 'entrada')->count(),
      'salidas' => Movimiento::where('TipoMovimiento', 'salida')->count(),
      'sin_orden' => Movimiento::whereNull('IdOrden')->count(),
    ];

    $materiales = Material::orderBy('Nombre')->get();

    return view('livewire.livmovimientos', [
      'movimientos' => $movimientos,
      'stats' => $stats,
      'materiales' => $materiales,
    ]);
  }

  // Limpiar filtros
  public function limpiarFiltros()
  {
    $this->search = '';
    $this->tipoMovimiento = '';
    $this->fechaDesde = '';
    $this->fechaHasta = '';
    $this->materialId = '';
    $this->resetPage();
  }

  public function verOrden($id)
  {
    // Redirigir a la orden específica
    return redirect()->to('/ordenes?edit=' . $id);
  }
}
