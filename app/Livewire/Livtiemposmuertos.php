<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tiemposmuertos;
use App\Models\User;
use Carbon\Carbon;

class Livtiemposmuertos extends Component
{
  // Filtros
  public $search = '';
  public $status = '';
  public $area = '';
  public $departamentoFiltro = '';
  public $fechaDesde = '';
  public $fechaHasta = '';

  // Campos del formulario
  public $editId = null;
  public $Name = '';
  public $EmployeeID = '';
  public $Area = '';
  public $ProductionLine = '';
  public $Description = '';
  public $Departament = '';
  public $DateOfOpen = '';
  public $Status = 'abierto';
  public $SolutionDescription = '';
  public $DateOfClose = '';
  public $TimeUsed = 0;

  // Control de modales
  public $showForm = false;
  public $showDetail = false;
  public $showCloseForm = false; // Nuevo modal para cerrar
  public $detailItem = null;
  public $closeItemId = null; // ID del item a cerrar

  // Usuario actual
  public $currentUser = null;

  // Lista de departamentos disponibles
  public $departamentosLista = [];

  public function mount()
  {
    $this->currentUser = auth()->user();
    $this->EmployeeID = $this->currentUser->NumeroEmpleado;
    $this->Name = $this->currentUser->Nombre;

    $this->departamentosLista = User::select('Departamento')
      ->whereNotNull('Departamento')
      ->distinct()
      ->pluck('Departamento')
      ->toArray();

    $isAdmin = in_array($this->currentUser->Position ?? '', ['Administrador', 'Admin', 'IT', 'ADMINISTRADOR']);
    if ($isAdmin) {
      $this->Departament = '';
    } else {
      $this->Departament = $this->currentUser->Departamento;
    }

    $this->DateOfOpen = Carbon::now()->format('Y-m-d\TH:i');
  }

  public function render()
  {
    $query = Tiemposmuertos::query();
    $isAdmin = in_array($this->currentUser->Position ?? '', ['Administrador', 'Admin', 'IT', 'ADMINISTRADOR']);

    if (!$isAdmin) {
      $query->where('Departament', $this->currentUser->Departamento);
    }

    // Búsqueda
    if ($this->search) {
      $query->where(function ($q) {
        $q->where('Name', 'LIKE', "%{$this->search}%")
          ->orWhere('Description', 'LIKE', "%{$this->search}%")
          ->orWhere('EmployeeID', 'LIKE', "%{$this->search}%")
          ->orWhere('ProductionLine', 'LIKE', "%{$this->search}%");
      });
    }

    if ($this->status) {
      $query->where('Status', $this->status);
    }

    if ($this->area) {
      $query->where('Area', 'LIKE', "%{$this->area}%");
    }

    if ($this->departamentoFiltro && $isAdmin) {
      $query->where('Departament', $this->departamentoFiltro);
    }

    if ($this->fechaDesde) {
      $query->whereDate('DateOfOpen', '>=', Carbon::parse($this->fechaDesde));
    }

    if ($this->fechaHasta) {
      $query->whereDate('DateOfOpen', '<=', Carbon::parse($this->fechaHasta));
    }

    $tiemposMuertos = $query->orderBy('DateOfOpen', 'desc')->paginate(15);

    $baseQuery = Tiemposmuertos::query();
    if (!$isAdmin) {
      $baseQuery->where('Departament', $this->currentUser->Departamento);
    }

    $stats = [
      'total' => $baseQuery->count(),
      'abiertos' => (clone $baseQuery)->where('Status', 'abierto')->count(),
      'cerrados' => (clone $baseQuery)->where('Status', 'cerrado')->count(),
      'tiempo_total' => (clone $baseQuery)->sum('TimeUsed'),
    ];

    $areas = Tiemposmuertos::select('Area')->distinct()->whereNotNull('Area')->pluck('Area');
    $departamentos = $isAdmin ? Tiemposmuertos::select('Departament')->distinct()->whereNotNull('Departament')->pluck('Departament') : collect();

    return view('livewire.livtiemposmuertos', [
      'tiemposMuertos' => $tiemposMuertos,
      'stats' => $stats,
      'areas' => $areas,
      'departamentos' => $departamentos,
      'isAdmin' => $isAdmin,
    ]);
  }

  public function abrirFormulario()
  {
    $this->resetErrorBag();
    $this->limpiarFormulario();
    $this->showForm = true;
    $this->EmployeeID = $this->currentUser->NumeroEmpleado;
    $this->Name = $this->currentUser->Nombre;

    $isAdmin = in_array($this->currentUser->Position ?? '', ['Administrador', 'Admin', 'IT', 'ADMINISTRADOR']);
    if (!$isAdmin) {
      $this->Departament = $this->currentUser->Departamento;
    } else {
      $this->Departament = '';
    }

    $this->DateOfOpen = Carbon::now()->format('Y-m-d\TH:i');
  }

  public function cerrarFormulario()
  {
    $this->showForm = false;
    $this->limpiarFormulario();
  }

  // Abrir modal para cerrar tiempo muerto
  public function abrirCerrar($id)
  {
    $item = Tiemposmuertos::find($id);

    // Verificar que el usuario pueda cerrar este registro
    $isAdmin = in_array($this->currentUser->Position ?? '', ['Administrador', 'Admin', 'IT', 'ADMINISTRADOR']);
    if (!$isAdmin && $item->Departament !== $this->currentUser->Departamento) {
      $this->dispatch(
        'showAlert',
        'No puedes cerrar un tiempo muerto de otro departamento',
        'error'
      );
      return;
    }

    $this->closeItemId = $id;
    $this->SolutionDescription = '';
    $this->TimeUsed = 0;
    $this->showCloseForm = true;
  }

  public function cerrarCerrarFormulario()
  {
    $this->showCloseForm = false;
    $this->closeItemId = null;
    $this->SolutionDescription = '';
    $this->TimeUsed = 0;
  }

  // Guardar cierre
  public function guardarCierre()
  {
    $this->validate([
      'SolutionDescription' => 'required|string',
      'TimeUsed' => 'required|integer|min:1',
    ]);

    $item = Tiemposmuertos::find($this->closeItemId);

    if (!$item) {
      $this->dispatch(
        'showAlert',
        'Registro no encontrado',
        'error'
      );
      return;
    }

    $item->update([
      'Status' => 'cerrado',
      'SolutionDescription' => $this->SolutionDescription,
      'DateOfClose' => Carbon::now(),
      'TimeUsed' => $this->TimeUsed,
    ]);

    $this->dispatch(
      'showAlert',
      'Tiempo muerto cerrado correctamente',
      'success'
    );
    $this->cerrarCerrarFormulario();
  }

  public function verDetalle($id)
  {
    $this->detailItem = Tiemposmuertos::find($id);
    $this->showDetail = true;
  }

  public function cerrarDetalle()
  {
    $this->showDetail = false;
    $this->detailItem = null;
  }

  public function guardar()
  {
    $isAdmin = in_array($this->currentUser->Position ?? '', ['Administrador', 'Admin', 'IT', 'ADMINISTRADOR']);

    $rules = [
      'Area' => 'required|string|max:255',
      'ProductionLine' => 'required|string|max:255',
      'Description' => 'required|string',
      'DateOfOpen' => 'required|date',
    ];

    if ($isAdmin) {
      $rules['Departament'] = 'required|string|max:255';
    }

    $this->validate($rules);

    if (!$isAdmin) {
      $this->Departament = $this->currentUser->Departamento;
    }

    $data = [
      'Name' => $this->Name,
      'EmployeeID' => $this->EmployeeID,
      'Area' => $this->Area,
      'ProductionLine' => $this->ProductionLine,
      'Description' => $this->Description,
      'Departament' => $this->Departament,
      'DateOfOpen' => Carbon::parse($this->DateOfOpen),
      'Status' => 'abierto', // Siempre abierto al crear
      'SolutionDescription' => null,
      'DateOfClose' => null,
      'TimeUsed' => 0,
      'user_departament' => $this->currentUser->Departamento,
    ];

    try {
      Tiemposmuertos::create($data);
      $this->dispatch(
        'showAlert',
        'Tiempo muerto registrado correctamente',
        'success'
      );
      $this->cerrarFormulario();
    } catch (\Exception $e) {
      $this->dispatch(
        'showAlert',
        'Error al guardar: ' . $e->getMessage(),
        'error'
      );
    }
  }

  public function limpiarFormulario()
  {
    $this->editId = null;
    $this->Area = '';
    $this->ProductionLine = '';
    $this->Description = '';
    $this->DateOfOpen = Carbon::now()->format('Y-m-d\TH:i');
    $this->Status = 'abierto';
    $this->SolutionDescription = '';
    $this->DateOfClose = '';
    $this->TimeUsed = 0;

    $this->EmployeeID = $this->currentUser->NumeroEmpleado;
    $this->Name = $this->currentUser->Nombre;

    $isAdmin = in_array($this->currentUser->Position ?? '', ['Administrador', 'Admin', 'IT', 'ADMINISTRADOR']);
    if (!$isAdmin) {
      $this->Departament = $this->currentUser->Departamento;
    } else {
      $this->Departament = '';
    }
  }

  public function limpiarFiltros()
  {
    $this->search = '';
    $this->status = '';
    $this->area = '';
    $this->departamentoFiltro = '';
    $this->fechaDesde = '';
    $this->fechaHasta = '';
  }
}
