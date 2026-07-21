<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Area;
use App\Models\Linea;
use App\Models\Maquina;

class Livareas extends Component
{
  use WithPagination;

  protected $paginationTheme = 'bootstrap';

  /*
    |--------------------------------------------------------------------------
    | FORMULARIOS
    |--------------------------------------------------------------------------
    */

  public $areaNombre = '';

  public $lineaNombre = '';
  public $selectedArea = '';

  public $maquinaNombre = '';
  public $selectedLinea = '';

  /*
    |--------------------------------------------------------------------------
    | EDICIÓN
    |--------------------------------------------------------------------------
    */

  public $editAreaId = null;
  public $editLineaId = null;
  public $editMaquinaId = null;

  /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

  public function render()
  {
    return view('livewire.livareas', [

      'areas' => Area::with('lineas.maquinas')
        ->orderBy('Nombre')
        ->paginate(10),

      'listaAreas' => Area::orderBy('Nombre')->get(),

      'listaLineas' => $this->selectedArea
        ? Linea::where('IdArea', $this->selectedArea)
        ->orderBy('Nombre')
        ->get()
        : collect(),

    ]);
  }

  /*
    |--------------------------------------------------------------------------
    | ÁREAS
    |--------------------------------------------------------------------------
    */

  public function guardarArea()
  {
    $this->validate([
      'areaNombre' => 'required|max:100'
    ]);

    Area::updateOrCreate(
      [
        'IdArea' => $this->editAreaId
      ],
      [
        'Nombre' => $this->areaNombre
      ]
    );

    $this->dispatch('showAlert', 'Área guardada correctamente', 'success');

    $this->resetArea();
  }

  public function editarArea($id)
  {
    $area = Area::findOrFail($id);

    $this->editAreaId = $area->IdArea;
    $this->areaNombre = $area->Nombre;
  }

  public function eliminarArea($id)
  {
    Area::findOrFail($id)->delete();

    $this->dispatch('showAlert', 'Área eliminada correctamente', 'success');
  }

  /*
    |--------------------------------------------------------------------------
    | LÍNEAS
    |--------------------------------------------------------------------------
    */

  public function guardarLinea()
  {
    $this->validate([
      'selectedArea' => 'required',
      'lineaNombre' => 'required|max:100'
    ]);
    Linea::updateOrCreate(
      [
        'IdLinea' => $this->editLineaId
      ],
      [
        'Nombre' => $this->lineaNombre,
        'IdArea' => $this->selectedArea
      ]
    );

    $this->dispatch('showAlert', 'Línea guardada correctamente', 'success');
    // $this->resetLinea();
  }

  public function editarLinea($id)
  {
    $linea = Linea::findOrFail($id);

    $this->editLineaId = $linea->IdLinea;
    $this->lineaNombre = $linea->Nombre;
    $this->selectedArea = $linea->IdArea;
  }

  public function eliminarLinea($id)
  {
    Linea::findOrFail($id)->delete();

    $this->dispatch('showAlert', 'Línea eliminada correctamente', 'success');
  }

  /*
    |--------------------------------------------------------------------------
    | MÁQUINAS
    |--------------------------------------------------------------------------
    */

  public function guardarMaquina()
  {
    $this->validate([
      'selectedLinea' => 'required',
      'maquinaNombre' => 'required|max:100'
    ]);

    Maquina::updateOrCreate(

      [
        'IdMaquina' => $this->editMaquinaId
      ],

      [
        'Nombre' => $this->maquinaNombre,
        'IdLinea' => $this->selectedLinea
      ]

    );

    $this->dispatch('showAlert', 'Máquina guardada correctamente', 'success');

    $this->resetMaquina();
  }

  public function editarMaquina($id)
  {
    $maquina = Maquina::with('linea')->findOrFail($id);

    $this->editMaquinaId = $maquina->IdMaquina;
    $this->maquinaNombre = $maquina->Nombre;

    $this->selectedLinea = $maquina->IdLinea;
    $this->selectedArea = $maquina->linea->IdArea;
  }

  public function eliminarMaquina($id)
  {
    Maquina::findOrFail($id)->delete();

    $this->dispatch('showAlert', 'Máquina eliminada correctamente', 'success');
  }

  /*
    |--------------------------------------------------------------------------
    | RESETS
    |--------------------------------------------------------------------------
    */

  public function resetArea()
  {
    $this->reset([
      'areaNombre',
      'editAreaId'
    ]);
  }

  public function resetLinea()
  {
    $this->reset([
      'lineaNombre',
      'selectedArea',
      'editLineaId'
    ]);
  }

  public function resetMaquina()
  {
    $this->reset([
      'maquinaNombre',
      'selectedLinea',
      'editMaquinaId'
    ]);
  }

  public function limpiar()
  {
    $this->reset([
      'areaNombre',
      'lineaNombre',
      'maquinaNombre',
      'selectedArea',
      'selectedLinea',
      'editAreaId',
      'editLineaId',
      'editMaquinaId'
    ]);

    $this->resetErrorBag();
  }
}
