<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Empleados;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmpleadosImport;
use Livewire\WithPagination;

class Livusuarios extends Component
{
  use WithFileUploads, WithPagination;

  public $NumeroEmpleado = '';
  public $Nombre = '';
  protected string $paginationTheme = 'bootstrap';
  public $archivo;


  public function render()
  {
    return view('livewire.livusuarios', [
      'empleados' => Empleados::orderBy('Nombre')
        ->paginate(12),
    ]);
  }

  protected $rules = [
    'NumeroEmpleado' => 'required|string|max:20|unique:empleados_listados,NumeroEmpleado',
    'Nombre' => 'required|string|max:255',
    'archivo' => 'nullable|file|mimes:xlsx,xls,csv|max:10240',
  ];

  protected $messages = [
    'NumeroEmpleado.required' => 'Ingrese el número de empleado.',
    'NumeroEmpleado.unique' => 'Ese empleado ya existe.',
    'Nombre.required' => 'Ingrese el nombre.',
  ];

  public function guardar()
  {
    $this->validate();

    Empleados::create([
      'NumeroEmpleado' => trim($this->NumeroEmpleado),
      'Nombre' => trim($this->Nombre),
    ]);

    $this->reset([
      'NumeroEmpleado',
      'Nombre'
    ]);

    $this->resetPage();

    $this->dispatch('showAlert', 'Empleado agregado correctamente', 'success');
  }

  public function eliminar($id)
  {
    Empleados::findOrFail($id)->delete();

    $this->resetPage();

    $this->dispatch('showAlert', 'Empleado eliminado correctamente', 'success');
  }

  public function importar()
  {
    $this->validate([
      'archivo' => 'required|file|mimes:xlsx,xls,csv|max:10240',
    ]);

    try {

      Excel::import(
        new EmpleadosImport,
        $this->archivo->getRealPath()
      );

      $this->reset('archivo');

      $this->dispatch(
        'showAlert',
        'Empleados importados correctamente.',
        'success'
      );
    } catch (\Exception $e) {

      $this->dispatch(
        'showAlert',
        'Error al importar: ' . $e->getMessage(),
        'error'
      );
    }
  }

  public function descargarPlantilla()
  {
    $ruta = public_path('plantillas/PlantillaEmpleados.xlsx');

    if (!file_exists($ruta)) {
      abort(404, 'No existe la plantilla.');
    }

    return response()->download($ruta);
  }
}
