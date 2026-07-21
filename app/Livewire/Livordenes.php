<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Orden;
use App\Models\Area;
use App\Models\User;
use App\Models\Linea;
use App\Models\Material;
use App\Models\Maquina;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\DB;
use App\Exports\OrdenesExport;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\WithPagination;

class Livordenes extends Component
{
  use WithPagination;
  protected $paginationTheme = 'bootstrap';
  public $perPage = 3;

  // CONSTANTES PARA ESTADOS
  private const ESTADOS_VALIDOS = ['abierta', 'en_proceso', 'cerrada'];
  private const ESTADOS_LEGIBLES = [
    'abierta' => 'Abierta',
    'en_proceso' => 'En proceso',
    'cerrada' => 'Cerrada'
  ];

  public $DescripcionArranque;
  public $HoraRecepcionLinea;
  public $HoraArranque;
  public $tiempoEspera = null;
  public $tiempoArranque = null;
  public $busquedaEmpleado = '';
  public $empleadosFiltrados = [];
  public $mostrarDropdown = false;
  public $showMovimientosModal = false;
  public $movimientosOrden = [];
  public $ordenSeleccionada = null;
  public $Engineer;

  // EDIT Y CAMPOS AUXILIARES
  public $editId = null;
  public $search = '';

  // NUEVOS FILTROS
  public $filtroArea = '';
  public $filtroLinea = '';
  public $filtroMaquina = '';
  public $filtroEstado = '';
  public $filtroFechaInicio = '';
  public $filtroFechaFin = '';
  public $filtroParoLinea = '';

  // NOMBRES PARA MOSTRAR
  public $areaNombre = '';
  public $lineaNombre = '';
  public $NombreEmpleado = '';

  // CAMPOS QUE LLENA Produccion
  public $tiempoTranscurrido;
  public $Descripcion;
  public $IdArea, $IdLinea;
  public $NumeroEmpleado;
  public $Maquina;

  // CAMPOS QUE LLENA Mantenimiento
  public $Procedimiento;
  public $ParoLinea = false;
  public $TiempoMuerto = 0;
  public $TiempoSolucion = null;
  public $ReqMaterial = false;
  public $Status = 'abierta';
  public $tiempoSolucionCalculado = null;

  // CAMPOS AUTOMÁTICOS
  public $Folio;
  public $HoraApertura;
  public $HoraCierre;
  public $Timestamp;

  // Dependencias
  public $lineasDisponibles = [];
  public $maquinasDisponibles = [];
  public $areasDisponibles = [];
  public $lineasFiltro = [];
  public $maquinasFiltro = [];

  // Materiales
  public $materiales = [];
  public $materialesSeleccionados = [];

  // Control de departamento
  public $departamento;

  public $Tipo = 'correctivo';
  public $Otro = '';

  /**
   * Obtener el departamento del usuario normalizado
   */
  private function getDepartamentoUsuario()
  {
    $user = auth()->user();

    if (!$user) {
      return 'Produccion';
    }
    return $user->Departamento ?? 'Produccion';
  }

  public function mount()
  {
    $this->materiales = Material::all();
    $this->materialesSeleccionados = [];
    $this->lineasDisponibles = collect();
    $this->Maquina = '';
    $this->search = '';
    $this->HoraRecepcionLinea = null;
    $this->HoraArranque = null;
    // Cargar áreas para filtros
    $this->areasDisponibles = Area::select('Nombre', DB::raw('MIN(IdArea) as IdArea'))
      ->groupBy('Nombre')
      ->get();
    // Asignar departamento del usuario
    $this->departamento = $this->getDepartamentoUsuario();
    if ($this->departamento === 'Produccion') {
      $this->limpiarCamposMtto();
    }
  }

  public function render()
  {
    // Construir consulta con filtros
    $query = Orden::with(['empleado', 'area', 'linea', 'movimientos.material']);

    // Búsqueda general
    if ($this->search) {
      $query->where(function ($q) {
        $q->where('Folio', 'LIKE', "%{$this->search}%")
          ->orWhere('Descripcion', 'LIKE', "%{$this->search}%")
          ->orWhere('Maquina', 'LIKE', "%{$this->search}%")
          ->orWhere('NumeroEmpleado', 'LIKE', "%{$this->search}%");
      });
    }

    // Filtros específicos
    if ($this->filtroArea) {
      $query->whereHas('area', function ($q) {
        $q->where('Nombre', $this->filtroArea);
      });
    }

    if ($this->filtroLinea) {
      $query->whereHas('linea', function ($q) {
        $q->where('Nombre', $this->filtroLinea);
      });
    }

    if ($this->filtroMaquina) {
      $query->where('Maquina', 'LIKE', "%{$this->filtroMaquina}%");
    }

    if ($this->filtroEstado) {
      $query->where('Status', $this->filtroEstado);
    }

    if ($this->filtroParoLinea !== '') {
      $query->where('ParoLinea', $this->filtroParoLinea);
    }

    if ($this->filtroFechaInicio) {
      $query->whereDate('HoraApertura', '>=', Carbon::parse($this->filtroFechaInicio)->startOfDay());
    }

    if ($this->filtroFechaFin) {
      $query->whereDate('HoraApertura', '<=', Carbon::parse($this->filtroFechaFin)->endOfDay());
    }

    $ordenes = $query
      ->latest('Timestamp')
      ->paginate($this->perPage);

    // Calcular tiempo de solución para cada orden
    foreach ($ordenes as $orden) {
      $orden->tiempo_solucion_calculado = $this->calcularTiempoSolucion($orden);
    }

    // Estadísticas para mostrar
    $coleccion = $ordenes->getCollection();

    $stats = [
      'total' => $ordenes->total(),
      'abiertas' => $coleccion->where('Status', 'abierta')->count(),
      'en_proceso' => $coleccion->where('Status', 'en_proceso')->count(),
      'cerradas' => $coleccion->where('Status', 'cerrada')->count(),
      'con_paro' => $coleccion->where('ParoLinea', true)->count(),
      'tiempo_total_muerto' => $coleccion->sum('TiempoMuerto'),
    ];

    return view('livewire.livordenes', [
      'ordenes' => $ordenes,
      'empleados' => User::query()
        ->when($this->busquedaEmpleado, function ($q) {
          $q->where('Nombre', 'like', "%{$this->busquedaEmpleado}%")
            ->orWhere('NumeroEmpleado', 'like', "%{$this->busquedaEmpleado}%");
        })
        ->limit(3)
        ->get(),
      'areas' => Area::select('Nombre', DB::raw('MIN(IdArea) as IdArea'))
        ->groupBy('Nombre')
        ->get(),
      'stats' => $stats,
      'lineasFiltro' => $this->lineasFiltro,
      'maquinasFiltro' => $this->maquinasFiltro,
      'departamentoActual' => $this->departamento,
    ]);
  }

  // Actualizar líneas cuando cambia el filtro de área
  public function updatedFiltroArea($value)
  {
    if ($value) {
      $area = Area::where('Nombre', $value)->first();
      if ($area) {
        $idsAreas = Area::where('Nombre', $area->Nombre)->pluck('IdArea');
        $this->lineasFiltro = Linea::whereIn('IdArea', $idsAreas)
          ->orderBy('Nombre')
          ->get();
        $this->filtroLinea = '';
        $this->maquinasFiltro = [];
        $this->filtroMaquina = '';
      }
    } else {
      $this->lineasFiltro = [];
      $this->maquinasFiltro = [];
      $this->filtroLinea = '';
      $this->filtroMaquina = '';
    }
  }

  // Actualizar máquinas cuando cambia el filtro de línea
  public function updatedFiltroLinea($value)
  {
    if ($value) {
      $this->maquinasFiltro = Maquina::where('IdLinea', $value)
        ->orderBy('Nombre')
        ->get();
      $this->filtroMaquina = '';
    } else {
      $this->maquinasFiltro = [];
      $this->filtroMaquina = '';
    }
  }

  // Limpiar todos los filtros
  public function limpiarFiltros()
  {
    $this->filtroArea = '';
    $this->filtroLinea = '';
    $this->filtroMaquina = '';
    $this->filtroEstado = '';
    $this->filtroFechaInicio = '';
    $this->filtroFechaFin = '';
    $this->filtroParoLinea = '';
    $this->lineasFiltro = [];
    $this->maquinasFiltro = [];
    $this->search = '';
  }

  /**
   * Calcular el tiempo de solución restando TiempoSolucion - HoraApertura
   * Retorna un string con días, horas y minutos
   */
  public function calcularTiempoSolucion($orden)
  {
    if (!$orden->TiempoSolucion || !$orden->HoraApertura) {
      return 'N/A';
    }
    try {
      $inicio = Carbon::parse($orden->HoraApertura)->startOfMinute();
      $fin = Carbon::parse($orden->TiempoSolucion)->startOfMinute();

      if ($fin->lt($inicio)) {
        return 'Fecha inválida';
      }
      // Diferencia total en minutos
      $minutos = $inicio->diffInMinutes($fin);

      // Sumar tiempo muerto si existe
      $tiempoMuerto = (int) ($orden->TiempoMuerto ?? 0);
      $minutos += $tiempoMuerto;

      // Convertir a días, horas y minutos
      $dias = intdiv($minutos, 1440); // 24 * 60
      $minutos %= 1440;

      $horas = intdiv($minutos, 60);
      $minutos %= 60;

      $partes = [];

      if ($dias > 0) {
        $partes[] = $dias . ' día' . ($dias != 1 ? 's' : '');
      }
      if ($horas > 0) {
        $partes[] = $horas . ' hora' . ($horas != 1 ? 's' : '');
      }
      if ($minutos > 0) {
        $partes[] = $minutos . ' minuto' . ($minutos != 1 ? 's' : '');
      }
      if (empty($partes)) {
        return '0 minutos';
      }
      return implode(', ', $partes);
    } catch (\Exception $e) {
      return 'Error en cálculo';
    }
  }

  // CALCULAR TIEMPOS DE ESPERA Y ARRANQUE
  public function calcularTiempos()
  {
    if ($this->HoraRecepcionLinea && $this->HoraApertura) {
      $recepcion = Carbon::parse($this->HoraRecepcionLinea);
      $apertura = Carbon::parse($this->HoraApertura);
      $diff = $recepcion->diff($apertura);

      if ($diff->d > 0) {
        $this->tiempoEspera = "{$diff->d}d {$diff->h}h {$diff->i}m";
      } elseif ($diff->h > 0) {
        $this->tiempoEspera = "{$diff->h}h {$diff->i}m";
      } elseif ($diff->i > 0) {
        $this->tiempoEspera = "{$diff->i} minutos";
      } else {
        $this->tiempoEspera = "{$diff->s} segundos";
      }
    } else {
      $this->tiempoEspera = null;
    }

    // Calcular tiempo de arranque (arranque - recepción)
    if ($this->HoraArranque && $this->HoraRecepcionLinea) {
      $arranque = Carbon::parse($this->HoraArranque);
      $recepcion = Carbon::parse($this->HoraRecepcionLinea);

      if ($arranque->gt($recepcion)) {
        $diff = $recepcion->diff($arranque);

        $this->tiempoArranque = match (true) {
          $diff->d > 0 => "{$diff->d}d {$diff->h}h {$diff->i}m",
          $diff->h > 0 => "{$diff->h}h {$diff->i}m",
          $diff->i > 0 => "{$diff->i} minutos",
          default => "{$diff->s} segundos"
        };
      } else {
        $this->tiempoArranque = null;
      }
    } else {
      $this->tiempoArranque = null;
    }
  }

  public function updatedHoraRecepcionLinea()
  {
    $this->calcularTiempos();
  }

  public function updatedHoraArranque()
  {
    $this->calcularTiempos();
  }

  public function updatedHoraApertura()
  {
    $this->calcularTiempos();
  }

  // ÁREA → LÍNEA
  public function updatedIdArea($value)
  {
    if ($value) {
      $area = Area::find($value);
      $idsAreas = Area::where('Nombre', $area->Nombre)
        ->pluck('IdArea');
      $this->lineasDisponibles = Linea::whereIn('IdArea', $idsAreas)
        ->get();
    } else {
      $this->lineasDisponibles = collect();
    }
    $this->IdLinea = null;
  }

  public function updatedIdLinea($value)
  {
    if ($value) {
      $this->maquinasDisponibles = Maquina::where('IdLinea', $value)
        ->orderBy('Nombre')
        ->get();
    } else {
      $this->maquinasDisponibles = collect();
    }
    $this->Maquina = null;
  }

  // MATERIAL (solo Mantenimiento e it)
  public function updatedReqMaterial($value)
  {
    if ($this->departamento === 'Produccion') return;

    if ($value && empty($this->materialesSeleccionados)) {
      $this->addMaterial();
    } elseif (!$value) {
      $this->materialesSeleccionados = [];
    }
  }

  public function updatedParoLinea($value)
  {
    if ($this->departamento === 'Produccion') return;

    if (!$value) {
      $this->TiempoMuerto = 0;
      $this->TiempoSolucion = null;
    }
  }

  // CALCULAR TIEMPO
  public function calcularTiempoTranscurrido()
  {
    if ($this->HoraApertura && $this->Status !== 'cerrada') {
      $inicio = Carbon::parse($this->HoraApertura);
      $ahora = Carbon::now();
      $this->tiempoTranscurrido = $inicio->diffForHumans($ahora, true);
    } elseif ($this->HoraApertura && $this->HoraCierre) {
      $inicio = Carbon::parse($this->HoraApertura);
      $fin = Carbon::parse($this->HoraCierre);
      $this->tiempoTranscurrido = $inicio->diffForHumans($fin, true);
    }
  }

  // CAMBIAR ESTADO (solo Mantenimiento e it)
  public function cambiarEstado($estado)
  {
    if ($this->departamento === 'Produccion') {
      $this->dispatch('showAlert', 'Solo Mantenimiento o IT pueden cambiar el estado', 'alert');
      return;
    }

    if (!in_array($estado, self::ESTADOS_VALIDOS, true)) {
      $estadoLegible = self::ESTADOS_LEGIBLES[$estado] ?? $estado;
      $this->dispatch('showAlert', "El estado \"{$estadoLegible}\" no es válido", 'error');
      return;
    }

    $this->Status = $estado;

    if ($estado === 'cerrada' && !$this->HoraCierre) {
      $this->HoraCierre = Carbon::now()->format('Y-m-d\TH:i');
      if (!$this->TiempoSolucion) {
        $this->TiempoSolucion = Carbon::now()->format('Y-m-d\TH:i');
      }
    } elseif ($estado === 'abierta') {
      $this->HoraCierre = null;
    }

    $this->calcularTiempoTranscurrido();

    $estadoMostrar = self::ESTADOS_LEGIBLES[$estado] ?? ucfirst($estado);
    $this->dispatch(
      'showAlert',
      "Estado cambiado a: {$estadoMostrar}",
      'success'
    );
  }

  // MATERIALES
  public function addMaterial()
  {
    $this->materialesSeleccionados[] = [
      'IdMaterial' => null,
      'CantidadUsada' => 1
    ];
  }

  public function removeMaterial($index)
  {
    unset($this->materialesSeleccionados[$index]);
    $this->materialesSeleccionados = array_values($this->materialesSeleccionados);
  }

  // VALIDACIÓN
  protected function rules()
  {
    if (!$this->editId && $this->departamento === 'Produccion') {
      return [
        'Descripcion' => 'required|string',
        'IdArea' => 'required|exists:areas,IdArea',
        'IdLinea' => 'required|exists:lineas,IdLinea',
        'NumeroEmpleado' => 'required|exists:users,NumeroEmpleado',
        'Maquina' => 'required|string|max:255',
      ];
    }

    if (in_array($this->departamento, ['Mantenimiento', 'it']) && $this->editId) {
      $rules = [
        'Procedimiento' => 'required|string',
        'TiempoSolucion' => 'nullable|date|date_format:Y-m-d\TH:i',
        'Tipo' => 'required|in:correctivo,mejora,instalación,Otro',
      ];

      // Validación condicional para campo 'Otro'
      if ($this->Tipo === 'Otro') {
        $rules['Otro'] = 'required|string|max:255';
      } else {
        $rules['Otro'] = 'nullable|string|max:255';
      }

      if ($this->ParoLinea) {
        $rules['TiempoMuerto'] = 'required|integer|min:0';
      } else {
        $rules['TiempoMuerto'] = 'nullable|integer|min:0';
      }

      // Validación personalizada para stock de materiales
      if ($this->ReqMaterial && !empty($this->materialesSeleccionados)) {
        foreach ($this->materialesSeleccionados as $index => $mat) {
          if (!empty($mat['IdMaterial']) && !empty($mat['CantidadUsada'])) {
            $material = Material::find($mat['IdMaterial']);
            if ($material && $material->Stock < $mat['CantidadUsada']) {
              $rules["materialesSeleccionados.{$index}.CantidadUsada"] = 'required|integer|min:1';
              $this->dispatch(
                'showAlert',
                "Stock insuficiente para {$material->Nombre}. Disponible: {$material->Stock}",
                'error'
              );
            }
          }
        }
      }

      return $rules;
    }

    return [];
  }

  // CREAR ORDEN (solo Produccion )
  public function guardar()
  {
    if ($this->departamento !== 'Produccion') {
      $this->dispatch(
        'showAlert',
        'Solo producción puede generar órdenes',
        'error'
      );
      return;
    }
    $this->validate([
      'Descripcion' => 'required|string',
      'IdArea' => 'required|exists:areas,IdArea',
      'IdLinea' => 'required|exists:lineas,IdLinea',
      'NumeroEmpleado' => 'required|exists:empleados,NumeroEmpleado',
      'Maquina' => 'required|string|max:255',
    ]);
    $ahora = Carbon::now();
    $folio = $ahora->format('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    $data = [
      'Folio' => $folio,
      'Descripcion' => $this->Descripcion,
      'IdArea' => $this->IdArea,
      'IdLinea' => $this->IdLinea,
      'NumeroEmpleado' => $this->NumeroEmpleado,
      'Maquina' => $this->Maquina,
      'Timestamp' => $ahora,
      'HoraApertura' => $ahora,
      'Status' => 'abierta',
      'Procedimiento' => null,
      'ParoLinea' => 0,
      'TiempoMuerto' => 0,
      'TiempoSolucion' => null,
      'ReqMaterial' => 0,
      'HoraCierre' => null,
      'HoraRecepcionLinea' => $this->HoraRecepcionLinea ? Carbon::parse($this->HoraRecepcionLinea) : null,
      'HoraArranque' => $this->HoraArranque ? Carbon::parse($this->HoraArranque) : null,
    ];
    Orden::create($data);
    $this->dispatch(
      'showAlert',
      "Orden creada. Folio: {$folio}",
      'success'
    );
    $this->limpiar();
  }

  // ACTUALIZAR ORDEN (solo Mantenimiento)
  public function actualizarMtto()
  {
    if ($this->departamento !== 'Mantenimiento' || !$this->editId) {
      $this->dispatch(
        'showAlert',
        'No autorizado',
        'error'
      );
      return;
    }

    $this->validate();

    DB::transaction(function () {
      $orden = Orden::findOrFail($this->editId);

      // ===============================
      // ACTUALIZAR DATOS GENERALES
      // ===============================
      $data = [
        'Procedimiento' => $this->Procedimiento,
        'ParoLinea' => $this->ParoLinea ? 1 : 0,
        'TiempoMuerto' => $this->ParoLinea ? ($this->TiempoMuerto ?? 0) : 0,
        'TiempoSolucion' => $this->TiempoSolucion ? Carbon::parse($this->TiempoSolucion) : null,
        'ReqMaterial' => $this->ReqMaterial ? 1 : 0,
        'Status' => $this->Status,
        'Tipo' => $this->Tipo,
        'Otro' => $this->Tipo === 'Otro' ? $this->Otro : null,
        'HoraArranque' => $this->HoraArranque ? Carbon::parse($this->HoraArranque) : null,
      ];

      // Si el estado es cerrada
      if ($this->Status === 'cerrada') {
        // Si no tiene hora de cierre, la establecemos
        if (!$orden->HoraCierre) {
          $data['HoraCierre'] = now();
        }

        // Si no hay tiempo de solución, lo establecemos al momento de cerrar
        if (!$this->TiempoSolucion) {
          $data['TiempoSolucion'] = now();
        }
      }

      $orden->update($data);

      // Procesar materiales
      if ($this->ReqMaterial && !empty($this->materialesSeleccionados)) {
        // Eliminar movimientos anteriores
        $orden->movimientos()->delete();

        foreach ($this->materialesSeleccionados as $mat) {
          if (!empty($mat['IdMaterial']) && !empty($mat['CantidadUsada'])) {
            // Verificar stock
            $material = Material::find($mat['IdMaterial']);
            if ($material && $material->Stock >= $mat['CantidadUsada']) {
              $orden->movimientos()->create([
                'IdMaterial' => $mat['IdMaterial'],
                'CantidadUsada' => $mat['CantidadUsada'],
                'TipoMovimiento' => 'salida'
              ]);

              // Actualizar stock
              $material->Stock -= $mat['CantidadUsada'];
              $material->save();
            }
          }
        }
      }
    });

    // Calcular el tiempo de solución para mostrar
    $ordenActualizada = Orden::find($this->editId);
    $this->tiempoSolucionCalculado = $this->calcularTiempoSolucion($ordenActualizada);

    $estadoMostrar = self::ESTADOS_LEGIBLES[$this->Status] ?? ucfirst($this->Status);
    $this->dispatch(
      'showAlert',
      "Orden {$estadoMostrar} correctamente",
      'success'
    );
    $this->limpiar();
  }

  public function cerrarOrden()
  {
    if (!auth()->check()) {
      $this->dispatch('showAlert', 'Debes iniciar sesión', 'error');
      return;
    }
    if ($this->departamento !== 'Mantenimiento') {
      $this->dispatch('showAlert', 'Solo Mantenimiento puede cerrar órdenes', 'error');
      return;
    }
    if (!$this->editId) {
      $this->dispatch('showAlert', 'No hay una orden seleccionada', 'error');
      return;
    }
    $orden = Orden::find($this->editId);
    if ($orden->Status !== 'abierta') {
      $this->dispatch('showAlert', 'Solo puedes cerrar órdenes abiertas', 'error');
      return;
    }
    $this->validate([
      'Procedimiento' => 'required|string|min:10',
      'Tipo' => 'required|in:correctivo,mejora,instalación,Otro',
      'ParoLinea' => 'boolean',
      'TiempoMuerto' => 'required_if:ParoLinea,true|integer|min:0',
      'ReqMaterial' => 'boolean',
    ]);
    if ($this->Tipo === 'Otro') {
      $this->validate(['Otro' => 'required|string|max:255']);
    }
    DB::transaction(function () {
      $orden = Orden::findOrFail($this->editId);

      $orden->update([
        'Procedimiento' => $this->Procedimiento,
        'Tipo' => $this->Tipo,
        'Otro' => $this->Tipo === 'Otro' ? $this->Otro : null,
        'ParoLinea' => $this->ParoLinea ? 1 : 0,
        'TiempoMuerto' => $this->ParoLinea ? ($this->TiempoMuerto ?? 0) : 0,
        'ReqMaterial' => $this->ReqMaterial ? 1 : 0,
        'Status' => 'cerrada',
        'HoraCierre' => now(),
        'TiempoSolucion' => now(),
      ]);

      // Procesar materiales
      if ($this->ReqMaterial && !empty($this->materialesSeleccionados)) {
        $orden->movimientos()->delete();
        foreach ($this->materialesSeleccionados as $mat) {
          if (!empty($mat['IdMaterial']) && !empty($mat['CantidadUsada'])) {
            $material = Material::find($mat['IdMaterial']);
            if ($material && $material->Stock >= $mat['CantidadUsada']) {
              $orden->movimientos()->create([
                'IdMaterial' => $mat['IdMaterial'],
                'CantidadUsada' => $mat['CantidadUsada'],
                'TipoMovimiento' => 'salida'
              ]);
              $material->Stock -= $mat['CantidadUsada'];
              $material->save();
            }
          }
        }
      }
    });

    $this->dispatch('showAlert', 'Orden cerrada correctamente', 'success');
    $this->limpiar();
  }

  public function editar($id)
  {
    if (!auth()->check()) {
      $this->dispatch('showAlert', 'Debes iniciar sesión', 'error');
      return;
    }
    $o = Orden::with(['movimientos', 'empleado', 'area', 'linea'])->findOrFail($id);
    if ($this->departamento === 'Produccion') {
      if ($o->Status !== 'cerrada') {
        $this->dispatch('showAlert', 'Solo puedes aprobar arranque en órdenes cerradas', 'error');
        return;
      }
      if ($o->HoraRecepcionLinea) {
        $this->dispatch('showAlert', 'Esta orden ya tiene arranque aprobado', 'error');
        return;
      }
    } elseif ($this->departamento === 'Mantenimiento') {
      // Mantenimiento SOLO puede editar órdenes ABIERTAS para cerrarlas
      if ($o->Status !== 'abierta') {
        $this->dispatch('showAlert', 'Solo puedes cerrar órdenes abiertas', 'error');
        return;
      }
    } elseif ($this->departamento === 'it') {
      // IT puede editar cualquier orden
    } else {
      $this->dispatch('showAlert', 'No autorizado', 'error');
      return;
    }
    // Cargar datos de la orden
    $this->editId = $o->IdOrden;
    $this->Folio = $o->Folio;
    $this->Descripcion = $o->Descripcion;
    $this->IdArea = $o->IdArea;
    $this->IdLinea = $o->IdLinea;
    $this->areaNombre = $o->area ? $o->area->Nombre : 'N/A';
    $this->lineaNombre = $o->linea ? $o->linea->Nombre : 'N/A';
    $this->NombreEmpleado = $o->empleado ? $o->empleado->Nombre : 'N/A';
    $this->NumeroEmpleado = $o->NumeroEmpleado;
    $this->Maquina = $o->Maquina;
    $this->HoraApertura = $o->HoraApertura ? Carbon::parse($o->HoraApertura)->format('Y-m-d\TH:i') : null;
    $this->Status = $o->Status;

    // Cargar campos según departamento
    if ($this->departamento === 'Produccion') {
      // Campos para aprobar arranque (orden ya cerrada por mantenimiento)
      $this->HoraRecepcionLinea = null;
      $this->HoraArranque = null;
      $this->DescripcionArranque = '';
    } elseif ($this->departamento === 'Mantenimiento') {
      // Campos para cerrar orden (orden abierta)
      $this->Procedimiento = $o->Procedimiento;
      $this->ParoLinea = (bool)$o->ParoLinea;
      $this->TiempoMuerto = $o->TiempoMuerto ?? 0;
      $this->ReqMaterial = (bool)$o->ReqMaterial;
      $this->Tipo = $o->Tipo ?? 'correctivo';
      $this->Otro = $o->Otro ?? '';

      if ($o->movimientos->count() > 0) {
        $this->materialesSeleccionados = $o->movimientos->map(function ($m) {
          return [
            'IdMaterial' => $m->IdMaterial,
            'CantidadUsada' => $m->CantidadUsada
          ];
        })->toArray();
      }
    }

    $this->calcularTiempos();
    $this->updatedIdArea($o->IdArea);
    $this->IdLinea = $o->IdLinea;
    // ============================================
    // DISPARAR EL MODAL CORRECTO SEGÚN DEPARTAMENTO
    // ============================================
    if ($this->departamento === 'Produccion') {
      $this->dispatch('abrirModalArranque');
    } elseif ($this->departamento === 'Mantenimiento') {
      $this->dispatch('abrirModalCerrar');
    }
  }

  public function limpiar()
  {
    $this->reset([
      'editId',
      'Folio',
      'Descripcion',
      'Procedimiento',
      'TiempoMuerto',
      'TiempoSolucion',
      'NumeroEmpleado',
      'Maquina',
      'IdArea',
      'IdLinea',
      'Status',
      'areaNombre',
      'lineaNombre',
      'NombreEmpleado',
      'Tipo',
      'Otro',
      'HoraRecepcionLinea',
      'HoraArranque',
      'tiempoEspera',
      'tiempoArranque',
      'tiempoSolucionCalculado'
    ]);

    $this->lineasDisponibles = collect();
    $this->materialesSeleccionados = [];
    $this->ReqMaterial = false;
    $this->ParoLinea = false;
    $this->HoraCierre = null;
    $this->tiempoTranscurrido = null;
    $this->IdArea = null;
    $this->IdLinea = null;
    $this->NumeroEmpleado = null;
    $this->Maquina = '';
    $this->Procedimiento = '';
    $this->Descripcion = '';
    $this->Tipo = 'correctivo';
    $this->Otro = '';
    $this->TiempoSolucion = null;
    $this->tiempoSolucionCalculado = null;

    if ($this->departamento !== 'Mantenimiento') {
      $this->limpiarCamposMtto();
    }
  }

  private function limpiarCamposMtto()
  {
    $this->Procedimiento = '';
    $this->ParoLinea = false;
    $this->TiempoMuerto = 0;
    $this->TiempoSolucion = null;
    $this->ReqMaterial = false;
    $this->materialesSeleccionados = [];
    $this->tiempoSolucionCalculado = null;
  }

  public function verMovimientos($id)
  {
    $orden = Orden::with('movimientos.material')->find($id);

    $this->ordenSeleccionada = $orden;
    $this->movimientosOrden = $orden->movimientos->map(function ($mov) {
      return [
        'id' => $mov->IdMovimiento,
        'material' => $mov->material ? $mov->material->Nombre : 'N/A',
        'cantidad' => $mov->CantidadUsada,
        'tipo' => $mov->TipoMovimiento,
        'fecha' => $mov->created_at ? $mov->created_at->format('d/m/Y H:i:s') : 'N/A'
      ];
    })->toArray();
    $this->showMovimientosModal = true;
  }

  public function cerrarModalMovimientos()
  {
    $this->showMovimientosModal = false;
    $this->movimientosOrden = [];
    $this->ordenSeleccionada = null;
  }

  public function updatedTipo($value)
  {
    if ($value !== 'Otro') {
      $this->Otro = '';
    }
  }

  public function exportarPDF($id)
  {
    $orden = Orden::with(['empleado', 'area', 'linea', 'movimientos.material'])
      ->findOrFail($id);
    $templatePath = public_path('templates/Orden.docx');
    if (!file_exists($templatePath)) {
      abort(404, 'Plantilla no encontrada');
    }
    $template = new TemplateProcessor($templatePath);
    // ===============================
    // DATOS GENERALES
    // ===============================
    $template->setValue('folio', $orden->Folio);
    $template->setValue('maquina', $orden->Maquina ?? '');
    $template->setValue('area', $orden->area->Nombre ?? 'N/A');
    $template->setValue('linea', $orden->linea->Nombre ?? 'N/A');
    $template->setValue('empleado', $orden->empleado->Nombre ?? 'N/A');
    $template->setValue('Engineer', auth()->user()->name ?? 'N/A');
    $template->setValue('ParoLinea', $orden->ParoLinea ? 'Si' : 'No');
    $template->setValue('descripcion', $orden->Descripcion ?? '');
    $template->setValue('Procedimiento', $orden->Procedimiento ?? '');

    // ===============================
    // FECHAS
    // ===============================
    $horaApertura = $orden->HoraApertura ? Carbon::parse($orden->HoraApertura) : null;
    $horaCierre   = $orden->HoraCierre ? Carbon::parse($orden->HoraCierre) : null;
    $tiempoSolucion = $orden->TiempoSolucion ? Carbon::parse($orden->TiempoSolucion) : null;
    $template->setValue('HoraApertura', $horaApertura ? $horaApertura->format('d/m/Y H:i') : '');
    $template->setValue('HoraCierre', $horaCierre ? $horaCierre->format('d/m/Y H:i') : '');
    $template->setValue('TiempoSolucion', $tiempoSolucion ? $tiempoSolucion->format('d/m/Y H:i') : 'N/A');

    // ===============================
    // TIEMPO DE SOLUCIÓN CALCULADO (Días, Horas, Minutos)
    // ===============================
    $tiempoSolucionCalculado = $this->calcularTiempoSolucion($orden);
    $template->setValue('TiempoSolucionCalculado', $tiempoSolucionCalculado);

    // ===============================
    // TIEMPOS
    // ===============================
    $template->setValue('TiempoMuerto', $orden->TiempoMuerto ?? 0);
    if ($horaApertura) {
      $tiempoMuerto = $orden->ParoLinea ? ($orden->TiempoMuerto ?? 0) : 0;
      $horaFin = $tiempoSolucion;
      $template->setValue('HoraFinCalculada', $horaFin->format('d/m/Y H:i'));
    } else {
      $template->setValue('HoraFinCalculada', '');
    }

    // ===============================
    // TIPO DE ORDEN (CHECKS)
    // ===============================
    collect(['correctivo', 'mejora', 'instalación', 'Otro'])
      ->each(function ($t) use ($template, $orden) {
        $template->setValue($t, $orden->Tipo === $t ? '✔' : '');
      });
    $template->setValue('Otrodesc', $orden->Otro ?? 'N/A');

    // ===============================
    // MATERIALES (TABLA DINÁMICA)
    // ===============================
    $movimientos = $orden->movimientos;
    $template->cloneRow('material', max(1, $movimientos->count()));
    if ($movimientos->count()) {
      foreach ($movimientos as $i => $mov) {
        $index = $i + 1;
        $template->setValue("material#{$index}", $mov->material->Nombre ?? 'N/A');
        $template->setValue("descripcionmat#{$index}", $mov->material->Descripcion ?? '');
        $template->setValue("cantidad#{$index}", $mov->CantidadUsada ?? 0);
      }
    } else {
      $template->setValue('material#1', 'Sin materiales');
      $template->setValue('descripcionmat#1', '');
      $template->setValue('cantidad#1', '');
    }

    // ===============================
    // GUARDAR Y DESCARGAR
    // ===============================
    $fileName = "Orden_{$orden->Folio}.docx";
    $filePath = storage_path("app/{$fileName}");
    $template->saveAs($filePath);
    return response()->download($filePath)->deleteFileAfterSend(true);
  }

  public function exportarExcel()
  {
    // Construir consulta con los mismos filtros que la vista
    $query = Orden::with(['empleado', 'area', 'linea', 'movimientos.material']);

    // Búsqueda general
    if ($this->search) {
      $query->where(function ($q) {
        $q->where('Folio', 'LIKE', "%{$this->search}%")
          ->orWhere('Descripcion', 'LIKE', "%{$this->search}%")
          ->orWhere('Maquina', 'LIKE', "%{$this->search}%")
          ->orWhere('NumeroEmpleado', 'LIKE', "%{$this->search}%");
      });
    }

    // Filtros específicos
    if ($this->filtroArea) {
      $query->whereHas('area', function ($q) {
        $q->where('Nombre', $this->filtroArea);
      });
    }

    if ($this->filtroLinea) {
      $query->whereHas('linea', function ($q) {
        $q->where('Nombre', $this->filtroLinea);
      });
    }

    if ($this->filtroMaquina) {
      $query->where('Maquina', 'LIKE', "%{$this->filtroMaquina}%");
    }

    if ($this->filtroEstado) {
      $query->where('Status', $this->filtroEstado);
    }

    if ($this->filtroParoLinea !== '') {
      $query->where('ParoLinea', $this->filtroParoLinea);
    }

    if ($this->filtroFechaInicio) {
      $query->whereDate('HoraApertura', '>=', Carbon::parse($this->filtroFechaInicio)->startOfDay());
    }

    if ($this->filtroFechaFin) {
      $query->whereDate('HoraApertura', '<=', Carbon::parse($this->filtroFechaFin)->endOfDay());
    }

    return Excel::download(
      new OrdenesExport($query),
      'Ordenes_Filtradas_' . Carbon::now()->format('Y-m-d_H-i') . '.xlsx'
    );
  }

  public function updatedBusquedaEmpleado()
  {
    $this->mostrarDropdown = true;

    $this->empleadosFiltrados = User::query()
      ->where(function ($q) {
        $q->where('Nombre', 'like', "%{$this->busquedaEmpleado}%")
          ->orWhere('NumeroEmpleado', 'like', "%{$this->busquedaEmpleado}%");
      })
      ->limit(5)
      ->get()
      ->toArray();

    if (empty($this->empleadosFiltrados)) {
      $this->empleadosFiltrados = User::limit(5)->get()->toArray();
    }
  }

  public function seleccionarEmpleado($numero, $nombre)
  {
    $this->NumeroEmpleado = $numero;
    $this->busquedaEmpleado = $numero . ' - ' . $nombre;
    $this->mostrarDropdown = false;
  }

  public function aprobarArranque()
  {
    if (!auth()->check()) {
      $this->dispatch('showAlert', 'Debes iniciar sesión', 'error');
      return;
    }

    // SOLO Produccion puede aprobar arranque
    if ($this->departamento !== 'Produccion') {
      $this->dispatch('showAlert', 'Solo producción puede aprobar el arranque', 'error');
      return;
    }

    if (!$this->editId) {
      $this->dispatch('showAlert', 'No hay una orden seleccionada', 'error');
      return;
    }

    // Verificar que la orden esté cerrada
    $orden = Orden::find($this->editId);
    if ($orden->Status !== 'cerrada') {
      $this->dispatch('showAlert', 'La orden debe estar cerrada para aprobar el arranque', 'error');
      return;
    }

    if ($orden->HoraRecepcionLinea) {
      $this->dispatch('showAlert', 'Esta orden ya tiene arranque aprobado', 'error');
      return;
    }

    $this->validate([
      'HoraRecepcionLinea' => 'required|date|date_format:Y-m-d\TH:i',
      'HoraArranque' => 'required|date|date_format:Y-m-d\TH:i|after:HoraRecepcionLinea',
      'DescripcionArranque' => 'required|string|max:500',
    ]);

    try {
      DB::transaction(function () {
        $orden = Orden::findOrFail($this->editId);
        $orden->update([
          'HoraRecepcionLinea' => Carbon::parse($this->HoraRecepcionLinea),
          'HoraArranque' => Carbon::parse($this->HoraArranque),
          'DescripcionArranque' => $this->DescripcionArranque,
          // La orden ya está cerrada por mantenimiento, no cambiamos el status
        ]);
      });

      $this->dispatch('showAlert', 'Arranque aprobado correctamente', 'success');
      $this->limpiar();
    } catch (\Exception $e) {
      $this->dispatch('showAlert', 'Error: ' . $e->getMessage(), 'error');
    }
  }

  public function eliminar($id)
  {
    if ($this->departamento !== 'it') {
      $this->dispatch('showAlert', 'No autorizado', 'error');
      return;
    }

    try {
      $orden = Orden::findOrFail($id);
      $folio = $orden->Folio;
      $orden->delete();

      $this->dispatch(
        'showAlert',
        "Orden {$folio} eliminada correctamente",
        'success'
      );
    } catch (\Exception $e) {
      $this->dispatch(
        'showAlert',
        'Error al eliminar la orden',
        'error'
      );
    }
  }
}
