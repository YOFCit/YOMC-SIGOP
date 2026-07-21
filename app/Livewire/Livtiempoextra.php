<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tiempoextra;
use App\Models\Empleados;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;

class Livtiempoextra extends Component
{
  public $NumeroEmpleado, $Nombre;
  public $Departamento, $FechaSolicitud;
  public $HorasExtra, $HoraInicio, $HoraFin;
  public $Descripcion, $Causas;
  public $Puesto;

  public $busquedaEmpleado = '';
  public $empleadosFiltrados = [];
  public $mostrarDropdown = false;

  public $registros = [];

  // ===== ROLES =====
  public function esAdmin()
  {
    return strtolower(trim(Auth::user()->Position)) === 'Administrador';
  }

  public function esJefe()
  {
    if (!Auth::check()) {
      return true;
    }
    return in_array(
      trim(Auth::user()->Position),
      ['Gerente', 'Supervisor', 'Ingeniero', 'Administrador']
    );
  }

  public function esMismoDepartamento($solicitudDepartamento)
  {
    if (!Auth::check()) {
      return false;
    }

    $departamentoUsuario = strtolower(trim(Auth::user()->Departamento));
    $departamentoSolicitud = strtolower(trim($solicitudDepartamento));

    return $departamentoUsuario === 'produccion'
      || $departamentoUsuario === $departamentoSolicitud;
  }

  public function esMismoDepto($registro)
  {
    if (!Auth::check()) {
      return false;
    }

    $departamentoUsuario = strtolower(trim(Auth::user()->Departamento));
    $departamentoRegistro = strtolower(trim($registro->Departamento));

    return $departamentoUsuario === 'produccion'
      || $departamentoUsuario === $departamentoRegistro;
  }
  // ===== VALIDACIÓN CENTRALIZADA 🔐 =====
  public function puedeAutorizar($registro)
  {
    return $this->esAdmin() || ($this->esJefe() && $this->esMismoDepto($registro));
  }

  // ===== OBTENER USUARIO AUTORIZADOR =====
  public function obtenerAutorizador($departamento)
  {
    $puestos = [
      'Ingeniería'    => 'Ingeniero',
      'Calidad'       => 'Ingeniero Calidad',
      'Producción'    => 'Supervisor Producción',
      'IT'            => 'Coordinador IT',
      'Mantenimiento' => 'Supervisor Mantenimiento',
    ];
    if (!isset($puestos[$departamento])) {
      return null;
    }
    return User::where('Departamento', $departamento)
      ->where('Position', $puestos[$departamento])
      ->first();
  }

  // ===== AUTOCOMPLETE =====
  public function updatedBusquedaEmpleado()
  {
    if (strlen($this->busquedaEmpleado) < 2) {
      $this->empleadosFiltrados = [];
      return;
    }
    $this->empleadosFiltrados = Empleados::query()
      ->where('Nombre', 'like', '%' . $this->busquedaEmpleado . '%')
      ->orWhere('NumeroEmpleado', 'like', '%' . $this->busquedaEmpleado . '%')
      ->limit(8)
      ->get()
      ->toArray();
  }

  public function seleccionarEmpleado($numero, $nombre)
  {
    $this->NumeroEmpleado = $numero;
    $this->Nombre = $nombre;
    $this->busquedaEmpleado = "{$numero} - {$nombre}";
    $this->empleadosFiltrados = [];
    $this->mostrarDropdown = false;
  }
  // ===== INIT =====
  public function mount()
  {
    $this->cargarRegistros();
  }

  public function cargarRegistros()
  {
    $query = Tiempoextra::query();
    $this->registros = $query->latest()->get();
  }

  // ===== GUARDAR =====
  public function guardar()
  {
    $this->validate([
      'NumeroEmpleado' => 'required',
      'Nombre' => 'required',
      'Departamento' => 'required',
      'FechaSolicitud' => 'required|date',
      'HoraInicio' => 'required',
      'HoraFin' => 'required',
      'Descripcion' => 'required',
      'Causas' => 'required',
    ]);

    $inicio = Carbon::parse($this->HoraInicio);
    $fin = Carbon::parse($this->HoraFin);
    if ($fin->lessThan($inicio)) {
      $fin->addDay();
    }
    $horasReales = $inicio->diffInMinutes($fin) / 60;

    $decimal = $horasReales - floor($horasReales);

    if ($decimal >= 0.9) {
      $horasExtra = ceil($horasReales);
    } else {
      $horasExtra = floor($horasReales);
    }

    Tiempoextra::create([
      'NumeroEmpleado' => $this->NumeroEmpleado,
      'Nombre' => $this->Nombre,
      'Departamento' => $this->Departamento,
      'FechaSolicitud' => $this->FechaSolicitud,
      'HoraInicio' => $this->HoraInicio,
      'HoraFin' => $this->HoraFin,
      'HorasExtra' => $horasExtra,
      'Descripcion' => $this->Descripcion,
      'Causas' => $this->Causas,
      'Solicitante' => auth()->user()->Nombre ?? 'Sistema',
      'Autorizador' => $this->obtenerAutorizador($this->Departamento),
      'Estatus' => 'Pendiente',
    ]);

    $this->reset([
      'NumeroEmpleado',
      'Nombre',
      'Departamento',
      'FechaSolicitud',
      'HoraInicio',
      'HoraFin',
      'HorasExtra',
      'Descripcion',
      'Causas',
      'busquedaEmpleado'
    ]);
    $this->cargarRegistros();
  }

  // ===== Calculo tiempo real =====
  public function updated($property)
  {
    if ($this->HoraInicio && $this->HoraFin) {

      $inicio = Carbon::parse($this->HoraInicio);
      $fin = Carbon::parse($this->HoraFin);

      if ($fin->lessThan($inicio)) {
        $fin->addDay();
      }

      $horasReales = $inicio->diffInMinutes($fin) / 60;

      $decimal = $horasReales - floor($horasReales);

      $this->HorasExtra = ($decimal >= 0.9)
        ? ceil($horasReales)
        : floor($horasReales);

      $this->HorasExtra = (int) $this->HorasExtra;
    }
  }
  // ===== AUTORIZAR =====
  public function autorizar($id)
  {
    $r = Tiempoextra::findOrFail($id);
    $user = Auth::user();
    if (!$this->puedeAutorizar($r)) {
      abort(403);
    }

    $r->update([
      'Estatus' => 'Autorizado',
      'AutorizadoPor' => $user->Nombre,
      'FechaAutorizacion' => now(),
    ]);

    $this->cargarRegistros();
  }

  // ===== RECHAZAR =====
  public function rechazar($id)
  {
    $r = Tiempoextra::findOrFail($id);
    $user = Auth::user();
    if (!$this->puedeAutorizar($r)) {
      abort(403);
    }

    $r->update([
      'Estatus' => 'Rechazado',
      'AutorizadoPor' => $user->Nombre,
      'FechaAutorizacion' => now(),
    ]);

    $this->cargarRegistros();
  }

  // ===== EXPORTAR WORD =====
  // ===== EXPORTAR WORD =====
  public function exportarWord($id)
  {
    $data = Tiempoextra::findOrFail($id);
    $user = Auth::user();
    $templatePath = public_path('templates/HR-F-01-007.docx');

    // Inicializar todos los checks vacíos
    $checks = [
      'calidad' => '',
      'produccion' => '',
      'mantenimiento' => '',
      'almacen' => '',
      'administracion' => '',
    ];

    // Mapeo de departamentos a las categorías de la plantilla
    $depto = strtolower(trim($data->Departamento));
    $depto = str_replace(
      ['á', 'é', 'í', 'ó', 'ú', 'ñ'],
      ['a', 'e', 'i', 'o', 'u', 'n'],
      $depto
    );

    // Asignar el check correspondiente según el departamento
    switch ($depto) {
      case 'calidad':
        $checks['calidad'] = 'X';
        break;
      case 'produccion':
      case 'it':
        $checks['produccion'] = 'X';
        break;
      case 'mantenimiento':
        $checks['mantenimiento'] = 'X';
        break;
      case 'almacen':
        $checks['almacen'] = 'X';
        break;
      case 'rh':
      case 'rrhh':
      case 'recursos humanos':
      case 'ingenieria':
      case 'finanzas':
      case 'cadena de suministro':
      case 'cadena de suministros':
        $checks['administracion'] = 'X';
        break;
      default:
        break;
    }

    if (!file_exists($templatePath)) {
      abort(404, 'Plantilla no encontrada');
    }

    $template = new TemplateProcessor($templatePath);

    // Datos básicos
    $template->setValue('Nombre', $data->Nombre);
    $template->setValue('NumeroEmpleado', $data->NumeroEmpleado);
    $template->setValue('Departamento', $data->Departamento);
    $template->setValue('FechaSolicitud', $data->FechaSolicitud);
    $template->setValue('HoraInicio', $data->HoraInicio);
    $template->setValue('HoraFin', $data->HoraFin);
    $template->setValue('HorasExtra', $data->HorasExtra ?? 0);
    $template->setValue('Descripcion', $data->Descripcion);
    $template->setValue('Causas', $data->Causas);
    $template->setValue('Solicitante', $user->Nombre ?? 'Sistema');
    // Fecha actual para la autorización
    $template->setValue('FechaAutorizacion', now()->format('d/m/Y'));

    // Checks
    $template->setValue('check_calidad', $checks['calidad']);
    $template->setValue('check_produccion', $checks['produccion']);
    $template->setValue('check_mantenimiento', $checks['mantenimiento']);
    $template->setValue('check_almacen', $checks['almacen']);
    $template->setValue('check_administracion', $checks['administracion']);

    // Generar archivo
    $fileName = "TiempoExtra_{$id}_{$data->NumeroEmpleado}.docx";
    $tempFile = storage_path("app/temp/{$fileName}");

    // Asegurar que el directorio temp existe
    if (!file_exists(storage_path('app/temp'))) {
      mkdir(storage_path('app/temp'), 0777, true);
    }

    $template->saveAs($tempFile);

    return response()->download($tempFile)->deleteFileAfterSend(true);
  }

  public function render()
  {
    return view('livewire.livtiempoextra');
  }
}
