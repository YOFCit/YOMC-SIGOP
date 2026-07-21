<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Material;
use Livewire\WithPagination;

class Livmateriales extends Component
{
  use WithPagination;
  protected $paginationTheme = 'bootstrap';

  public $IdMaterial, $Nombre, $Descripcion, $Location, $Stock;
  public $editId = null;
  public $search = '';
  public $materialAEliminar = null;
  public $nombreMaterial;

  public function updatingSearch()
  {
    $this->resetPage();
  }

  public function render()
  {
    $query = Material::query();

    if ($this->search) {
      $query->where(function ($q) {
        $q->where('Nombre', 'LIKE', "%{$this->search}%")
          ->orWhere('Descripcion', 'LIKE', "%{$this->search}%")
          ->orWhere('Location', 'LIKE', "%{$this->search}%");
      });
    }

    $materiales = $query->orderBy('Nombre')->paginate(10);

    $stats = [
      'total' => Material::count(),
      'stock_total' => Material::sum('Stock'),
      'bajo_stock' => Material::where('Stock', '<=', 5)->count(),
    ];

    return view('livewire.livmateriales', [
      'materiales' => $materiales,
      'stats' => $stats,
    ]);
  }

  protected function rules()
  {
    return [
      'Nombre' => 'required|string|max:255',
      'Descripcion' => 'required|string',
      'Location' => 'required|string|max:255',
      'Stock' => 'required|numeric|min:0'
    ];
  }

  public function guardar()
  {
    $this->validate();

    Material::updateOrCreate(
      ['IdMaterial' => $this->editId],
      [
        'Nombre' => $this->Nombre,
        'Descripcion' => $this->Descripcion,
        'Location' => $this->Location,
        'Stock' => $this->Stock
      ]
    );
    $this->dispatch(
      'showAlert',
      $this->editId
        ? 'Material actualizado correctamente'
        : 'Material registrado correctamente',
      'success'
    );
    $this->limpiar();
  }

  public function editar($id)
  {
    $mat = Material::find($id);

    $this->editId = $mat->IdMaterial;
    $this->Nombre = $mat->Nombre;
    $this->Descripcion = $mat->Descripcion;
    $this->Location = $mat->Location;
    $this->Stock = $mat->Stock;
  }

  public function eliminar($id)
  {
    $material = Material::find($id);
    if (!$material) {
      $this->dispatch('showAlert', 'Material no encontrado', 'error');
      return;
    }
    $tieneEntradas = $material->movimientos()
      ->where('tipo', 'entrada')
      ->exists();
    $tieneSalidas = $material->movimientos()
      ->where('tipo', 'salida')
      ->exists();
    if ($tieneEntradas && $tieneSalidas) {
      $this->dispatch(
        'showAlert',
        'No puedes eliminar el material porque ya tiene movimientos de entrada y salida',
        'warning'
      );
      return;
    }
    $material->delete();
    $this->dispatch('showAlert', 'Material eliminado correctamente', 'success');
    $this->materialAEliminar = null;
  }

  public function confirmarEliminacion($id)
  {
    $mat = Material::find($id);
    $this->materialAEliminar = $id;
    $this->nombreMaterial = $mat->Nombre;
  }

  public function limpiar()
  {
    $this->reset(['Nombre', 'Descripcion', 'Location', 'Stock', 'editId']);
    $this->resetErrorBag();
  }

  public function cancelar()
  {
    $this->limpiar();
  }

  public function limpiarFiltros()
  {
    $this->search = '';
    $this->resetPage();
  }
}
