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
use Illuminate\Support\Collection;

class Livordenes extends Component
{
  use WithPagination;

  protected $paginationTheme = 'bootstrap';
  public $perPage = 3;

  // ============================================================
  // CONSTANTES CENTRALIZADAS
  // ============================================================
  private const ESTADOS_VALIDOS = ['abierta', 'en_proceso', 'cerrada'];

  private const ESTADOS_LEGIBLES = [
    'abierta'     => 'Abierta',
    'en_proceso'  => 'En proceso',
    'cerrada'     => 'Cerrada'
  ];

  /**
   * Mapa de permisos: acción => [departamentos permitidos]
   * ÚNICO LUGAR DONDE SE DEFINEN LOS PERMISOS
   */
  private const PERMISOS = [
    'crear_orden'       => ['Produccion'],
    'cerrar_orden'      => ['Mantenimiento','IT'],
    'editar_completo'   => ['IT'],
    'aprobar_arranque'  => ['Produccion'],
    'eliminar_orden'    => ['IT'],
    'ver_materiales'    => ['Mantenimiento', 'IT'],
    'exportar'          => ['Produccion', 'Mantenimiento', 'IT'],
  ];

  // ============================================================
  // PROPIEDADES DEL FORMULARIO (todas agrupadas)
  // ============================================================

  // --- Campos de producción ---
  public $Descripcion;
  public $IdArea;
  public $IdLinea;
  public $NumeroEmpleado;
  public $Maquina;
  public $HoraRecepcionLinea;
  public $HoraArranque;
  public $DescripcionArranque;

  // --- Campos de mantenimiento ---
  public $Procedimiento;
  public $ParoLinea = false;
  public $TiempoMuerto = 0;
  public $TiempoSolucion = null;
  public $ReqMaterial = false;
  public $Status = 'abierta';
  public $Tipo = 'correctivo';
  public $Otro = '';

  // --- Campos calculados/automáticos ---
  public $Folio;
  public $HoraApertura;
  public $HoraCierre;
  public $Timestamp;
  public $tiempoTranscurrido;
  public $tiempoEspera = null;
  public $tiempoArranque = null;
  public $tiempoSolucionCalculado = null;

  // --- Nombres para mostrar ---
  public $areaNombre = '';
  public $lineaNombre = '';
  public $NombreEmpleado = '';
  public $Engineer;

  // --- Edición ---
  public $editId = null;

  // --- Búsqueda empleado ---
  public $busquedaEmpleado = '';
  public $empleadosFiltrados = [];
  public $mostrarDropdown = false;

  // --- Materiales ---
  public $materiales = [];
  public $materialesSeleccionados = [];

  // --- Modal movimientos ---
  public $showMovimientosModal = false;
  public $movimientosOrden = [];
  public $ordenSeleccionada = null;

  // ============================================================
  // FILTROS
  // ============================================================
  public $search = '';
  public $filtroArea = '';
  public $filtroLinea = '';
  public $filtroMaquina = '';
  public $filtroEstado = '';
  public $filtroFechaInicio = '';
  public $filtroFechaFin = '';
  public $filtroParoLinea = '';

  // --- Dependencias para selects ---
  public $areasDisponibles = [];
  public $lineasDisponibles = [];
  public $maquinasDisponibles = [];
  public $lineasFiltro = [];
  public $maquinasFiltro = [];

  // --- Departamento del usuario actual ---
  public  $departamentoUsuario;

  // ============================================================
  // CICLO DE VIDA
  // ============================================================

  public function mount()
  {
    $this->departamentoUsuario = $this->obtenerDepartamentoUsuario();
    $this->areasDisponibles = $this->cargarAreas();
    $this->materiales = Material::all();
    $this->materialesSeleccionados = [];
    $this->lineasDisponibles = collect();
    $this->maquinasDisponibles = collect();
    $this->filtroFechaInicio = now()->subMonth()->format('Y-m-d');
    $this->filtroFechaFin = now()->format('Y-m-d');
    $this->Maquina = '';
    $this->search = '';
    $this->HoraRecepcionLinea = null;
    $this->HoraArranque = null;
  }

  public function render()
  {
    return view('livewire.livordenes', [
      'ordenes'             => $this->obtenerOrdenesPaginadas(),
      'empleados'           => $this->buscarEmpleados($this->busquedaEmpleado),
      'areas'               => Area::orderBy('Nombre')->get(),
      'lineasFiltro'        => $this->cargarLineasFiltro(),
      'maquinasFiltro'      => $this->cargarMaquinasFiltro(),
      'stats'               => $this->calcularEstadisticas(),
      'departamentoActual'  => $this->departamentoUsuario,
    ]);
  }

    // ============================================================
    // MÉTODOS DE AUTORIZACIÓN (ÚNICO PUNTO DE CONTROL)
    // ============================================================

  /**
   * Obtener el departamento del usuario autenticado
   */
  private function obtenerDepartamentoUsuario(): string
  {
    $user = auth()->user();
    return $user ? ($user->Departamento ?? 'Produccion') : 'Produccion';
  }

  /**
   * Verificar si el usuario tiene permiso para una acción
   */
  private function tienePermiso(string $accion): bool
  {
    return in_array($this->departamentoUsuario, self::PERMISOS[$accion] ?? []);
  }

  /**
   * Autorizar o lanzar error
   */
  private function autorizar(string $accion, string $mensaje = 'No autorizado'): void
  {
    if (!$this->tienePermiso($accion)) {
      $this->dispatch('showAlert', $mensaje, 'error');
      throw new \Exception($mensaje);
    }
  }

  /**
   * Verificar si el usuario pertenece a un departamento específico
   */
  private function esDepartamento(string $departamento): bool
  {
    return $this->obtenerDepartamentoUsuario() === $departamento;
  }

    // ============================================================
    // MÉTODOS DE CONSULTA Y FILTRADO
    // ============================================================

  /**
   * Construir query base con filtros aplicados
   */
  private function queryBase()
  {
    $query = Orden::query()->with(['empleado', 'area', 'linea', 'movimientos.material']);

    // Búsqueda general
    if (!empty($this->search)) {
      $query->where(function ($q) {
        $q->where('Folio', 'like', '%' . $this->search . '%')
          ->orWhere('Descripcion', 'like', '%' . $this->search . '%')
          ->orWhere('Maquina', 'like', '%' . $this->search . '%')
          ->orWhere('NumeroEmpleado', 'like', '%' . $this->search . '%')
          ->orWhereHas('empleado', fn($e) => $e->where('Nombre', 'like', '%' . $this->search . '%'));
      });
    }

    // Filtro área
    if (!empty($this->filtroArea)) {
      $query->where('IdArea', $this->filtroArea);
    }

    // Filtro estado
    if (!empty($this->filtroEstado)) {
      $query->where('Status', $this->filtroEstado);
    }

    // Filtro paro de línea
    if ($this->filtroParoLinea !== '' && $this->filtroParoLinea !== null) {
      $query->where('ParoLinea', (int) $this->filtroParoLinea);
    }

    // Filtro fechas
    if (!empty($this->filtroFechaInicio)) {
      $query->whereDate('HoraApertura', '>=', $this->filtroFechaInicio);
    }
    if (!empty($this->filtroFechaFin)) {
      $query->whereDate('HoraApertura', '<=', $this->filtroFechaFin);
    }

    return $query;
  }

  /**
   * Obtener órdenes paginadas con tiempo de solución calculado
   */
  private function obtenerOrdenesPaginadas()
  {
    $ordenes = $this->queryBase()
      ->orderByDesc('Timestamp')
      ->paginate($this->perPage);

    $ordenes->getCollection()->transform(function ($orden) {
      $orden->tiempo_solucion_calculado = $this->calcularTiempoSolucion($orden);
      return $orden;
    });

    return $ordenes;
  }

  /**
   * Calcular estadísticas basadas en los filtros actuales
   */
  private function calcularEstadisticas(): array
  {
    $base = $this->queryBase();

    return [
      'total'              => (clone $base)->count(),
      'abiertas'           => (clone $base)->where('Status', 'abierta')->count(),
      'en_proceso'         => (clone $base)->where('Status', 'en_proceso')->count(),
      'cerradas'           => (clone $base)->where('Status', 'cerrada')->count(),
      'con_paro'           => (clone $base)->where('ParoLinea', 1)->count(),
      'tiempo_total_muerto' => (clone $base)->sum('TiempoMuerto'),
    ];
  }

  /**
   * Buscar empleados por nombre o número
   */
  private function buscarEmpleados(string $busqueda, int $limite = 5): Collection
  {
    return User::query()
      ->when($busqueda, function ($q) use ($busqueda) {
        $q->where(function ($sub) use ($busqueda) {
          $sub->where('Nombre', 'like', "%{$busqueda}%")
            ->orWhere('NumeroEmpleado', 'like', "%{$busqueda}%");
        });
      })
      ->limit($limite)
      ->get();
  }

  /**
   * Cargar áreas agrupadas para selects
   */
  private function cargarAreas(): Collection
  {
    return Area::select('Nombre', DB::raw('MIN(IdArea) as IdArea'))
      ->groupBy('Nombre')
      ->orderBy('Nombre')
      ->get();
  }

  /**
   * Cargar líneas para el filtro según área seleccionada
   */
  private function cargarLineasFiltro(): Collection
  {
    if (!$this->filtroArea) {
      return collect();
    }

    $area = Area::where('Nombre', $this->filtroArea)->first();
    if (!$area) {
      return collect();
    }

    $idsAreas = Area::where('Nombre', $area->Nombre)->pluck('IdArea');
    return Linea::whereIn('IdArea', $idsAreas)->orderBy('Nombre')->get();
  }

  /**
   * Cargar máquinas para el filtro según línea seleccionada
   */
  private function cargarMaquinasFiltro(): Collection
  {
    if (!$this->filtroLinea) {
      return collect();
    }

    return Maquina::where('IdLinea', $this->filtroLinea)->orderBy('Nombre')->get();
  }

    // ============================================================
    // CÁLCULOS DE TIEMPO
    // ============================================================

  /**
   * Calcular tiempo de solución (HoraApertura -> TiempoSolucion + TiempoMuerto)
   */
  public function calcularTiempoSolucion($orden): string
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

      $minutosTotales = $inicio->diffInMinutes($fin) + (int) ($orden->TiempoMuerto ?? 0);
      return $this->formatearMinutos($minutosTotales);
    } catch (\Exception $e) {
      return 'Error en cálculo';
    }
  }

  /**
   * Calcular tiempos de espera y arranque
   */
  private function calcularTiemposEsperaArranque(): void
  {
    // Tiempo de espera (recepción - apertura)
    $this->tiempoEspera = null;
    if ($this->HoraRecepcionLinea && $this->HoraApertura) {
      $recepcion = Carbon::parse($this->HoraRecepcionLinea);
      $apertura = Carbon::parse($this->HoraApertura);
      $this->tiempoEspera = $this->calcularDiferenciaLegible($recepcion, $apertura);
    }

    // Tiempo de arranque (arranque - recepción)
    $this->tiempoArranque = null;
    if ($this->HoraArranque && $this->HoraRecepcionLinea) {
      $arranque = Carbon::parse($this->HoraArranque);
      $recepcion = Carbon::parse($this->HoraRecepcionLinea);
      if ($arranque->gt($recepcion)) {
        $this->tiempoArranque = $this->calcularDiferenciaLegible($recepcion, $arranque);
      }
    }
  }

  /**
   * Calcular diferencia entre dos fechas y devolver string legible
   */
  private function calcularDiferenciaLegible(Carbon $inicio, Carbon $fin): string
  {
    $minutos = $inicio->diffInMinutes($fin);
    return $this->formatearMinutos($minutos);
  }

  /**
   * Formatear minutos totales a días, horas, minutos
   */
  private function formatearMinutos(int $minutosTotales): string
  {
    $dias = intdiv($minutosTotales, 1440);
    $minutosTotales %= 1440;
    $horas = intdiv($minutosTotales, 60);
    $minutos = $minutosTotales % 60;

    $partes = [];
    if ($dias > 0)   $partes[] = $dias . ' día' . ($dias != 1 ? 's' : '');
    if ($horas > 0)  $partes[] = $horas . ' hora' . ($horas != 1 ? 's' : '');
    if ($minutos > 0) $partes[] = $minutos . ' minuto' . ($minutos != 1 ? 's' : '');

    return empty($partes) ? '0 minutos' : implode(', ', $partes);
  }

  /**
   * Calcular tiempo transcurrido desde apertura
   */
  private function actualizarTiempoTranscurrido(): void
  {
    if ($this->HoraApertura && $this->Status !== 'cerrada') {
      $inicio = Carbon::parse($this->HoraApertura);
      $this->tiempoTranscurrido = $inicio->diffForHumans(Carbon::now(), true);
    } elseif ($this->HoraApertura && $this->HoraCierre) {
      $inicio = Carbon::parse($this->HoraApertura);
      $fin = Carbon::parse($this->HoraCierre);
      $this->tiempoTranscurrido = $inicio->diffForHumans($fin, true);
    }
  }

  // ============================================================
  // MANEJO DE DEPENDENCIAS (ÁREA -> LÍNEA -> MÁQUINA)
  // ============================================================

  public function updatedIdArea($value)
  {
    if ($value) {
      $area = Area::find($value);
      if ($area) {
        $idsAreas = Area::where('Nombre', $area->Nombre)->pluck('IdArea');
        $this->lineasDisponibles = Linea::whereIn('IdArea', $idsAreas)->orderBy('Nombre')->get();
      }
    } else {
      $this->lineasDisponibles = collect();
    }
    $this->reset(['IdLinea', 'Maquina']);
  }

  public function updatedIdLinea($value)
  {
    if ($value) {
      $this->maquinasDisponibles = Maquina::where('IdLinea', $value)->orderBy('Nombre')->get();
    } else {
      $this->maquinasDisponibles = collect();
    }
    $this->Maquina = null;
  }

  // ============================================================
  // MANEJO DE MATERIALES
  // ============================================================

  public function addMaterial()
  {
    $this->materialesSeleccionados[] = ['IdMaterial' => null, 'CantidadUsada' => 1];
  }

  public function removeMaterial($index)
  {
    unset($this->materialesSeleccionados[$index]);
    $this->materialesSeleccionados = array_values($this->materialesSeleccionados);
  }

  public function updatedReqMaterial($value)
  {
    if (!$this->tienePermiso('cerrar_orden') && !$this->tienePermiso('editar_completo')) {
      return;
    }

    if ($value && empty($this->materialesSeleccionados)) {
      $this->addMaterial();
    } elseif (!$value) {
      $this->materialesSeleccionados = [];
    }
  }

  /**
   * Validar stock disponible para materiales seleccionados
   */
  private function validarStockMateriales(): bool
  {
    if (!$this->ReqMaterial || empty($this->materialesSeleccionados)) {
      return true;
    }

    foreach ($this->materialesSeleccionados as $mat) {
      if (empty($mat['IdMaterial']) || empty($mat['CantidadUsada'])) {
        continue;
      }

      $material = Material::find($mat['IdMaterial']);
      if ($material && $material->Stock < $mat['CantidadUsada']) {
        $this->dispatch(
          'showAlert',
          "Stock insuficiente para {$material->Nombre}. Disponible: {$material->Stock}",
          'error'
        );
        return false;
      }
    }

    return true;
  }

  /**
   * Procesar movimientos de materiales para una orden (crear movimientos y descontar stock)
   */
  private function procesarMateriales(Orden $orden): void
  {
    if (!$this->ReqMaterial || empty($this->materialesSeleccionados)) {
      return;
    }

    DB::transaction(function () use ($orden) {
      $orden->movimientos()->delete();

      foreach ($this->materialesSeleccionados as $mat) {
        if (empty($mat['IdMaterial']) || empty($mat['CantidadUsada'])) {
          continue;
        }

        $material = Material::find($mat['IdMaterial']);
        if (!$material || $material->Stock < $mat['CantidadUsada']) {
          continue;
        }

        $orden->movimientos()->create([
          'IdMaterial'    => $mat['IdMaterial'],
          'CantidadUsada' => $mat['CantidadUsada'],
          'TipoMovimiento' => 'salida',
        ]);

        $material->decrement('Stock', $mat['CantidadUsada']);
      }
    });
  }

    // ============================================================
    // ACCIONES PRINCIPALES (CRUD)
    // ============================================================

  /**
   * CREAR ORDEN - Solo Producción
   */
  public function guardar()
  {
    $this->autorizar('crear_orden', 'Solo producción puede generar órdenes');

    $this->validate([
      'Descripcion'     => 'required|string',
      'IdArea'          => 'required|exists:areas,IdArea',
      'IdLinea'         => 'required|exists:lineas,IdLinea',
      'NumeroEmpleado'  => 'required|exists:empleados,NumeroEmpleado',
      'Maquina'         => 'required|string|max:255',
    ]);

    $ahora = Carbon::now();
    $folio = $ahora->format('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

    Orden::create([
      'Folio'               => $folio,
      'Descripcion'         => $this->Descripcion,
      'IdArea'              => $this->IdArea,
      'IdLinea'             => $this->IdLinea,
      'NumeroEmpleado'      => $this->NumeroEmpleado,
      'Maquina'             => $this->Maquina,
      'Timestamp'           => $ahora,
      'HoraApertura'        => $ahora,
      'Status'              => 'abierta',
      'HoraRecepcionLinea'  => $this->HoraRecepcionLinea ? Carbon::parse($this->HoraRecepcionLinea) : null,
      'HoraArranque'        => $this->HoraArranque ? Carbon::parse($this->HoraArranque) : null,
    ]);

    $this->dispatch('showAlert', "Orden creada. Folio: {$folio}", 'success');
    $this->limpiarFormulario();
  }

  /**
   * CERRAR ORDEN - Solo Mantenimiento
   */
  public function cerrarOrden()
  {
    $this->autorizar('cerrar_orden', 'Solo Mantenimiento puede cerrar órdenes');
    if (!$this->editId) {
      $this->dispatch('showAlert', 'No hay una orden seleccionada', 'error');
      return;
    }
    $orden = Orden::findOrFail($this->editId);
    if ($orden->Status !== 'abierta' && $orden->Status !== 'en_proceso') {
      $this->dispatch('showAlert', 'Solo puedes cerrar órdenes abiertas', 'error');
      return;
    }
    $this->validarFormularioCierre();
    if (!$this->validarStockMateriales()) {
      return;
    }
    DB::transaction(function () use ($orden) {
      $orden->update([
        'Procedimiento'  => $this->Procedimiento,
        'Tipo'           => $this->Tipo,
        'Otro'           => $this->Tipo === 'Otro' ? $this->Otro : null,
        'ParoLinea'      => $this->ParoLinea ? 1 : 0,
        'TiempoMuerto'   => $this->ParoLinea ? ($this->TiempoMuerto ?? 0) : 0,
        'ReqMaterial'    => $this->ReqMaterial ? 1 : 0,
        'Status'         => 'cerrada',
        'HoraCierre'     => now(),
        'TiempoSolucion' => now(),
      ]);
      $this->procesarMateriales($orden);
    });

    $this->dispatch('showAlert', 'Orden cerrada correctamente', 'success');
    $this->limpiarFormulario();
  }

  /**
   * APROBAR ARRANQUE - Solo Producción/IT
   */
  public function aprobarArranque()
  {
    $this->autorizar('aprobar_arranque', 'Solo producción o IT pueden aprobar el arranque');

    if (!$this->editId) {
      $this->dispatch('showAlert', 'No hay una orden seleccionada', 'error');
      return;
    }

    $orden = Orden::findOrFail($this->editId);

    if ($orden->Status !== 'cerrada') {
      $this->dispatch('showAlert', 'La orden debe estar cerrada para aprobar el arranque', 'error');
      return;
    }

    if ($orden->HoraRecepcionLinea) {
      $this->dispatch('showAlert', 'Esta orden ya tiene arranque aprobado', 'error');
      return;
    }

    $this->validate([
      'HoraRecepcionLinea'  => 'required|date|date_format:Y-m-d\TH:i',
      'HoraArranque'        => 'required|date|date_format:Y-m-d\TH:i|after:HoraRecepcionLinea',
      'DescripcionArranque' => 'required|string|max:500',
    ]);

    $orden->update([
      'HoraRecepcionLinea'  => Carbon::parse($this->HoraRecepcionLinea),
      'HoraArranque'        => Carbon::parse($this->HoraArranque),
      'DescripcionArranque' => $this->DescripcionArranque,
    ]);

    $this->dispatch('showAlert', 'Arranque aprobado correctamente', 'success');
    $this->limpiarFormulario();
  }

  /**
   * EDITAR ORDEN COMPLETA - Solo IT
   */
  public function actualizarMtto()
  {
    $this->autorizar('editar_completo', 'No autorizado');

    if (!$this->editId) {
      $this->dispatch('showAlert', 'No hay una orden seleccionada', 'error');
      return;
    }

    $this->validarFormularioCierre();

    if (!$this->validarStockMateriales()) {
      return;
    }

    DB::transaction(function () {
      $orden = Orden::findOrFail($this->editId);

      $data = [
        'Procedimiento'  => $this->Procedimiento,
        'ParoLinea'      => $this->ParoLinea ? 1 : 0,
        'TiempoMuerto'   => $this->ParoLinea ? ($this->TiempoMuerto ?? 0) : 0,
        'TiempoSolucion' => $this->TiempoSolucion ? Carbon::parse($this->TiempoSolucion) : null,
        'ReqMaterial'    => $this->ReqMaterial ? 1 : 0,
        'Status'         => $this->Status,
        'Tipo'           => $this->Tipo,
        'Otro'           => $this->Tipo === 'Otro' ? $this->Otro : null,
      ];

      if ($this->Status === 'cerrada') {
        if (!$orden->HoraCierre) {
          $data['HoraCierre'] = now();
        }
        if (!$this->TiempoSolucion) {
          $data['TiempoSolucion'] = now();
        }
      }

      $orden->update($data);
      $this->procesarMateriales($orden);
    });

    $estadoMostrar = self::ESTADOS_LEGIBLES[$this->Status] ?? ucfirst($this->Status);
    $this->dispatch('showAlert', "Orden actualizada ({$estadoMostrar})", 'success');
    $this->limpiarFormulario();
  }

  /**
   * ELIMINAR ORDEN - Solo IT
   */
  public function eliminar($id)
  {
    $this->autorizar('eliminar_orden', 'No autorizado');

    try {
      $orden = Orden::findOrFail($id);
      $folio = $orden->Folio;
      $orden->delete();
      $this->dispatch('showAlert', "Orden {$folio} eliminada correctamente", 'success');
    } catch (\Exception $e) {
      $this->dispatch('showAlert', 'Error al eliminar la orden', 'error');
    }
  }

    // ============================================================
    // CARGA DE ORDEN PARA EDICIÓN
    // ============================================================

  /**
   * Cargar orden en el formulario según el departamento
   */
  public function editar($id)
  {
    $orden = Orden::with(['movimientos', 'empleado', 'area', 'linea'])->findOrFail($id);

    $this->validarAccesoEdicion($orden);
    $this->cargarDatosBasicosOrden($orden);

    $departamento = $this->obtenerDepartamentoUsuario();

    // Para aprobar arranque: HoraRecepcionLinea = HoraCierre (bloqueado)
    if (
      $departamento === 'Produccion' ||
      ($departamento === 'IT' && $orden->Status === 'cerrada' && !$orden->HoraRecepcionLinea)
    ) {
      $this->HoraRecepcionLinea = $orden->HoraCierre
        ? Carbon::parse($orden->HoraCierre)->format('Y-m-d\TH:i')
        : null;
    }

    if ($departamento === 'Mantenimiento' || $departamento === 'IT') {
      $this->cargarDatosMantenimiento($orden);
    }

    // Cargar líneas y máquinas sin resetear Maquina
    if ($this->IdArea) {
      $area = Area::find($this->IdArea);
      if ($area) {
        $idsAreas = Area::where('Nombre', $area->Nombre)->pluck('IdArea');
        $this->lineasDisponibles = Linea::whereIn('IdArea', $idsAreas)->orderBy('Nombre')->get();
      }
    }

    if ($this->IdLinea) {
      $this->maquinasDisponibles = Maquina::where('IdLinea', $this->IdLinea)->orderBy('Nombre')->get();
    }
  }
  /**
   * Validar si el usuario puede editar esta orden
   */
  private function validarAccesoEdicion(Orden $orden): void
  {
    $departamento = $this->obtenerDepartamentoUsuario();

    if ($departamento === 'Produccion') {
      if ($orden->Status !== 'cerrada') {
        throw new \Exception('Solo puedes aprobar arranque en órdenes cerradas');
      }
      if ($orden->HoraRecepcionLinea) {
        throw new \Exception('Esta orden ya tiene arranque aprobado');
      }
    } elseif ($departamento === 'Mantenimiento') {
      if ($orden->Status !== 'abierta') {
        throw new \Exception('Solo puedes cerrar órdenes abiertas');
      }
    }
  }

  /**
   * Cargar datos básicos de la orden
   */
  private function cargarDatosBasicosOrden(Orden $orden): void
  {
    $this->editId          = $orden->IdOrden;
    $this->Folio           = $orden->Folio;
    $this->Descripcion     = $orden->Descripcion;
    $this->IdArea          = $orden->IdArea;
    $this->IdLinea         = $orden->IdLinea;
    $this->areaNombre      = $orden->area?->Nombre ?? 'N/A';
    $this->lineaNombre     = $orden->linea?->Nombre ?? 'N/A';
    $this->NombreEmpleado  = $orden->empleado?->Nombre ?? 'N/A';
    $this->NumeroEmpleado  = $orden->NumeroEmpleado;
    $this->Maquina         = $orden->Maquina; // Cargar ANTES de actualizar dependencias
    $this->HoraApertura    = $orden->HoraApertura ? Carbon::parse($orden->HoraApertura)->format('Y-m-d\TH:i') : null;
    $this->Status          = $orden->Status;
    $this->HoraCierre      = $orden->HoraCierre ? Carbon::parse($orden->HoraCierre)->format('Y-m-d\TH:i') : null;
  }

  /**
   * Cargar datos específicos de mantenimiento
   */
  private function cargarDatosMantenimiento(Orden $orden): void
  {
    $this->Procedimiento = $orden->Procedimiento;
    $this->ParoLinea     = (bool) $orden->ParoLinea;
    $this->TiempoMuerto  = $orden->TiempoMuerto ?? 0;
    $this->ReqMaterial   = (bool) $orden->ReqMaterial;
    $this->Tipo          = $orden->Tipo ?? 'correctivo';
    $this->Otro          = $orden->Otro ?? '';

    if ($orden->movimientos->count() > 0) {
      $this->materialesSeleccionados = $orden->movimientos->map(function ($m) {
        return [
          'IdMaterial'    => $m->IdMaterial,
          'CantidadUsada' => $m->CantidadUsada,
        ];
      })->toArray();
    }
  }

  /**
   * Disparar el modal correcto según departamento
   */
  private function dispatchModalEdicion(): void
  {
    if ($this->esDepartamento('Produccion')) {
      $this->dispatch('abrirModalArranque');
    } elseif ($this->esDepartamento('Mantenimiento')) {
      $this->dispatch('abrirModalCerrar');
    } elseif ($this->esDepartamento('IT')) {
      $orden = Orden::find($this->editId);
      if ($orden && $orden->Status === 'cerrada' && !$orden->HoraRecepcionLinea) {
        $this->dispatch('abrirModalArranque');
      } else {
        $this->dispatch('abrirModalEditar');
      }
    }
  }

    // ============================================================
    // VALIDACIONES
    // ============================================================

  /**
   * Validar formulario de cierre/edición
   */
  private function validarFormularioCierre(): void
  {
    $rules = [
      'Procedimiento' => 'required|string|min:10',
      'Tipo'          => 'required|in:correctivo,mejora,instalación,Otro',
    ];

    if ($this->Tipo === 'Otro') {
      $rules['Otro'] = 'required|string|max:255';
    }

    if ($this->ParoLinea) {
      $rules['TiempoMuerto'] = 'required|integer|min:0';
    }

    $this->validate($rules);
  }

    // ============================================================
    // EXPORTACIONES
    // ============================================================

  /**
   * Exportar a Excel
   */
  public function exportarExcel()
  {
    $this->autorizar('exportar', 'No tienes permiso para exportar');

    return Excel::download(
      new OrdenesExport($this->queryBase()),
      'Ordenes_' . now()->format('Y-m-d_H-i') . '.xlsx'
    );
  }

  /**
   * Exportar a PDF (Word)
   */
  public function exportarPDF($id)
  {
    $this->autorizar('exportar', 'No tienes permiso para exportar');

    $orden = Orden::with(['empleado', 'area', 'linea', 'movimientos.material'])->findOrFail($id);

    $templatePath = public_path('templates/Orden.docx');
    if (!file_exists($templatePath)) {
      abort(404, 'Plantilla no encontrada');
    }

    $template = new TemplateProcessor($templatePath);
    $this->llenarPlantillaWord($template, $orden);

    $fileName = "Orden_{$orden->Folio}.docx";
    $filePath = storage_path("app/{$fileName}");
    $template->saveAs($filePath);

    return response()->download($filePath)->deleteFileAfterSend(true);
  }

  /**
   * Llenar plantilla Word con datos de la orden
   */
  private function llenarPlantillaWord(TemplateProcessor $template, Orden $orden): void
  {
    // Datos generales
    $template->setValue('folio', $orden->Folio);
    $template->setValue('maquina', $orden->Maquina ?? '');
    $template->setValue('area', $orden->area?->Nombre ?? 'N/A');
    $template->setValue('linea', $orden->linea?->Nombre ?? 'N/A');
    $template->setValue('empleado', $orden->empleado?->Nombre ?? 'N/A');
    $template->setValue('Engineer', auth()->user()?->name ?? 'N/A');
    $template->setValue('ParoLinea', $orden->ParoLinea ? 'Si' : 'No');
    $template->setValue('descripcion', $orden->Descripcion ?? '');
    $template->setValue('Procedimiento', $orden->Procedimiento ?? '');
    $template->setValue('TiempoSolucionCalculado', $this->calcularTiempoSolucion($orden));
    $template->setValue('TiempoMuerto', $orden->TiempoMuerto ?? 0);

    // Fechas
    $template->setValue('HoraApertura', $orden->HoraApertura ? Carbon::parse($orden->HoraApertura)->format('d/m/Y H:i') : '');
    $template->setValue('HoraCierre', $orden->HoraCierre ? Carbon::parse($orden->HoraCierre)->format('d/m/Y H:i') : '');
    $template->setValue('TiempoSolucion', $orden->TiempoSolucion ? Carbon::parse($orden->TiempoSolucion)->format('d/m/Y H:i') : 'N/A');
    $template->setValue('HoraFinCalculada', $orden->TiempoSolucion ? Carbon::parse($orden->TiempoSolucion)->format('d/m/Y H:i') : '');

    // Tipo de orden (checkboxes)
    foreach (['correctivo', 'mejora', 'instalación', 'Otro'] as $tipo) {
      $template->setValue($tipo, $orden->Tipo === $tipo ? '✔' : '');
    }
    $template->setValue('Otrodesc', $orden->Otro ?? 'N/A');

    // Materiales
    $movimientos = $orden->movimientos;
    $template->cloneRow('material', max(1, $movimientos->count()));

    if ($movimientos->count() > 0) {
      foreach ($movimientos as $i => $mov) {
        $idx = $i + 1;
        $template->setValue("material#{$idx}", $mov->material?->Nombre ?? 'N/A');
        $template->setValue("descripcionmat#{$idx}", $mov->material?->Descripcion ?? '');
        $template->setValue("cantidad#{$idx}", $mov->CantidadUsada ?? 0);
      }
    } else {
      $template->setValue('material#1', 'Sin materiales');
      $template->setValue('descripcionmat#1', '');
      $template->setValue('cantidad#1', '');
    }
  }

  // ============================================================
  // MODAL DE MOVIMIENTOS
  // ============================================================

  public function verMovimientos($id)
  {
    $orden = Orden::with('movimientos.material')->find($id);

    $this->ordenSeleccionada = $orden;
    $this->movimientosOrden = $orden->movimientos->map(function ($mov) {
      return [
        'id'       => $mov->IdMovimiento,
        'material' => $mov->material?->Nombre ?? 'N/A',
        'cantidad' => $mov->CantidadUsada,
        'tipo'     => $mov->TipoMovimiento,
        'fecha'    => $mov->created_at?->format('d/m/Y H:i:s') ?? 'N/A',
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

    // ============================================================
    // UTILIDADES
    // ============================================================

  /**
   * Limpiar completamente el formulario
   */
  public function limpiarFormulario()
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
      'DescripcionArranque',
      'tiempoEspera',
      'tiempoArranque',
      'tiempoSolucionCalculado',
      'tiempoTranscurrido',
      'HoraCierre',
    ]);

    $this->lineasDisponibles = collect();
    $this->maquinasDisponibles = collect();
    $this->materialesSeleccionados = [];
    $this->ReqMaterial = false;
    $this->ParoLinea = false;
    $this->Tipo = 'correctivo';
    $this->Otro = '';
    $this->Status = 'abierta';
  }

  /**
   * Seleccionar empleado del dropdown
   */
  public function seleccionarEmpleado($numero, $nombre)
  {
    $this->NumeroEmpleado = $numero;
    $this->busquedaEmpleado = $numero . ' - ' . $nombre;
    $this->mostrarDropdown = false;
  }

  /**
   * Actualizar búsqueda de empleados
   */
  public function updatedBusquedaEmpleado()
  {
    $this->mostrarDropdown = true;
    $this->empleadosFiltrados = $this->buscarEmpleados($this->busquedaEmpleado)->toArray();
  }

  /**
   * Manejar cambio de tipo de mantenimiento
   */
  public function updatedTipo($value)
  {
    if ($value !== 'Otro') {
      $this->Otro = '';
    }
  }

  /**
   * Manejar cambio de paro de línea
   */
  public function updatedParoLinea($value)
  {
    if ($this->esDepartamento('Produccion')) {
      return;
    }

    if (!$value) {
      $this->TiempoMuerto = 0;
      $this->TiempoSolucion = null;
    }
  }

  /**
   * Calcular tiempos cuando cambian horas
   */
  public function updatedHoraRecepcionLinea()
  {
    $this->calcularTiemposEsperaArranque();
  }

  public function updatedHoraArranque()
  {
    $this->calcularTiemposEsperaArranque();
  }

  public function updatedHoraApertura()
  {
    $this->calcularTiemposEsperaArranque();
  }

  /**
   * Limpiar todos los filtros
   */
  public function limpiarFiltros()
  {
    $this->reset([
      'filtroArea',
      'filtroLinea',
      'filtroMaquina',
      'filtroEstado',
      'filtroFechaInicio',
      'filtroFechaFin',
      'filtroParoLinea',
      'search',
    ]);
    $this->lineasFiltro = [];
    $this->maquinasFiltro = [];
  }

  /**
   * Actualizar líneas del filtro
   */
  public function updatedFiltroArea($value)
  {
    if ($value) {
      $this->lineasFiltro = $this->cargarLineasFiltro();
      $this->reset(['filtroLinea', 'filtroMaquina', 'maquinasFiltro']);
    } else {
      $this->reset(['lineasFiltro', 'maquinasFiltro', 'filtroLinea', 'filtroMaquina']);
    }
  }

  /**
   * Actualizar máquinas del filtro
   */
  public function updatedFiltroLinea($value)
  {
    if ($value) {
      $this->maquinasFiltro = $this->cargarMaquinasFiltro();
      $this->filtroMaquina = '';
    } else {
      $this->maquinasFiltro = [];
      $this->filtroMaquina = '';
    }
  }

  public function cambiarEstadoOrden($id, $estado)
  {
    $orden = Orden::findOrFail($id);
    $orden->update(['Status' => $estado]);
  }
}
