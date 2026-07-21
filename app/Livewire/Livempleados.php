<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;

class Livempleados extends Component
{
  use WithPagination;
  protected $paginationTheme = 'bootstrap';

  public $NumeroEmpleado, $Nombre, $Email, $Position, $Departamento, $password;
  public $editId = null;
  public $search = '';
  public $empleadoAEliminar = null;
  public $nombreEmpleado = null;

  public function updatingSearch()
  {
    $this->resetPage();
  }

  public function render()
  {
    $query = User::query();

    if ($this->search) {
      $query->where(function ($q) {
        $q->where('NumeroEmpleado', 'LIKE', "%{$this->search}%")
          ->orWhere('Nombre', 'LIKE', "%{$this->search}%")
          ->orWhere('Position', 'LIKE', "%{$this->search}%")
          ->orWhere('Departamento', 'LIKE', "%{$this->search}%");
      });
    }

    $empleados = $query->orderBy('Nombre')->paginate(10);

    // Estadísticas
    $stats = [
      'total' => User::count(),
    ];

    return view('livewire.livempleados', [
      'empleados' => $empleados,
      'stats' => $stats,
    ]);
  }

  protected function rules()
  {
    return [
      'NumeroEmpleado' => 'required|numeric|unique:users,NumeroEmpleado,' . $this->editId . ',NumeroEmpleado',
      'Nombre' => 'required|string|max:255',
      'Email' => 'required|email|max:255|unique:users,Email,' . $this->editId . ',NumeroEmpleado',
      'Position' => 'required|string|max:255',
      'Departamento' => 'required|string|max:255',
      'password' => $this->editId ? 'nullable|min:4' : 'required|min:4',
    ];
  }

  public function guardar()
  {
    $this->validate();

    $data = [
      'NumeroEmpleado' => $this->NumeroEmpleado,
      'Nombre' => $this->Nombre,
      'Email' => $this->Email,
      'Position' => $this->Position,
      'Departamento' => $this->Departamento,
    ];

    if ($this->password) {
      $data['password'] = Hash::make($this->password);
    }

    if ($this->editId) {
      User::where('NumeroEmpleado', $this->editId)->update($data);
    } else {
      User::create($data);
    }

    $this->dispatch(
      'showAlert',
      $this->editId
        ? 'Empleado actualizado correctamente'
        : 'Empelado registrado correctamente',
      'success'
    );
    $this->limpiar();
  }

  public function editar($id)
  {
    $empleado = User::find($id);

    $this->editId = $empleado->NumeroEmpleado;
    $this->NumeroEmpleado = $empleado->NumeroEmpleado;
    $this->Nombre = $empleado->Nombre;
    $this->Position = $empleado->Position;
    $this->Departamento = $empleado->Departamento;
    $this->Email = $empleado->Email;
    $this->password = '';
  }

  public function eliminar()
  {
    $empleado = User::find($this->empleadoAEliminar);

    if (!$empleado) {
      $this->dispatch('showAlert', 'Empleado no encontrado', 'error');
      return;
    }

    $empleado->delete();

    $this->dispatch('showAlert', 'Empleado eliminado correctamente', 'success');

    // limpiar estado
    $this->empleadoAEliminar = null;
    $this->nombreEmpleado = null;
  }

  public function confirmarEliminacion($id)
  {
    $emp = User::find($id);

    if (!$emp) {
      $this->dispatch('showAlert', 'Empleado no encontrado', 'error');
      return;
    }

    $this->empleadoAEliminar = $id;
    $this->nombreEmpleado = $emp->Nombre;
  }

  public function limpiar()
  {
    $this->reset([
      'NumeroEmpleado',
      'Nombre',
      'Email',
      'Position',
      'Departamento',
      'password',
      'editId'
    ]);
    
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
