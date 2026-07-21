<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Orden;
use Asantibanez\LivewireCharts\Models\PieChartModel;
use Asantibanez\LivewireCharts\Models\ColumnChartModel;
use Asantibanez\LivewireCharts\Models\LineChartModel;

class LivInicio extends Component
{
  //=========================
  // KPIs ADMIN
  //=========================
  public int $totalOrdenes = 0;
  public int $ordenesAbiertas = 0;
  public int $ordenesProceso = 0;
  public int $ordenesCerradas = 0;

  //=========================
  // KPIs TIEMPOS MUERTOS
  //=========================
  public int $totalTiempoMuerto = 0;
  public int $tiempoMuertoMes = 0;
  public array $tiemposMuertosPorMes = [];

  //=========================
  // KPIs EMPLEADO
  //=========================
  public int $misOrdenes = 0;
  public int $misOrdenesAbiertas = 0;
  public int $misOrdenesProceso = 0;
  public int $misOrdenesCerradas = 0;

  // Variables para almacenar los modelos de gráficas
  protected $estadoChartModel;
  protected $areaChartModel;
  protected $mesChartModel;
  protected $tiemposMuertosChartModel;

  // Estado de carga
  public bool $cargando = true;

  public function mount()
  {
    $this->cargarDashboard();
    $this->inicializarGraficas();
    $this->cargando = false;
  }

  public function cargarNombre()
  {
    return auth()->user()?->Nombre;
  }

  public function esVistaAdministrador(): bool
  {
    $user = auth()->user();
    if (!$user) {
      return false;
    }
    return in_array($user->Position, ['Administrador', 'Gerente'])
      || in_array($user->Departamento, ['Mantenimiento']);
  }

  public function cargarDashboard()
  {
    $user = auth()->user();
    if (!$user) {
      return;
    }

    if ($this->esVistaAdministrador()) {
      $this->cargarAdministrador();
      $this->cargarTiemposMuertos();
    } else {
      $this->cargarEmpleado($user);
    }
  }

  private function cargarAdministrador()
  {
    try {
      $this->totalOrdenes = Orden::count();

      $estado = Orden::select(
        'Status',
        DB::raw('count(*) total')
      )
        ->groupBy('Status')
        ->pluck('total', 'Status');

      $this->ordenesAbiertas = (int) ($estado['abierta'] ?? 0);
      $this->ordenesProceso = (int) ($estado['en_proceso'] ?? 0);
      $this->ordenesCerradas = (int) ($estado['cerrada'] ?? 0);
    } catch (\Throwable $e) {
      Log::error('LivInicio::cargarAdministrador - ' . $e->getMessage());
      $this->totalOrdenes = 0;
      $this->ordenesAbiertas = 0;
      $this->ordenesProceso = 0;
      $this->ordenesCerradas = 0;
    }
  }

  private function cargarTiemposMuertos()
  {
    try {
      // Total de tiempos muertos (suma de todas las órdenes)
      $this->totalTiempoMuerto = (int) (Orden::sum('TiempoMuerto') ?? 0)/60;

      // Tiempo muerto del mes actual
      $this->tiempoMuertoMes = (int) (Orden::whereMonth('Timestamp', now()->month)
        ->whereYear('Timestamp', now()->year)
        ->sum('TiempoMuerto') ?? 0)/60;

      // Tiempos muertos por mes (últimos 12 meses)
      $this->tiemposMuertosPorMes = [];
      for ($i = 11; $i >= 0; $i--) {
        $fecha = now()->subMonths($i);
        $total = Orden::whereYear('Timestamp', $fecha->year)
          ->whereMonth('Timestamp', $fecha->month)
          ->sum('TiempoMuerto') ?? 0;

        $this->tiemposMuertosPorMes[$fecha->format('M Y')] = $total;
      }

      // DEBUG: Descomenta para verificar los datos
      // dd([
      //     'total' => $this->totalTiempoMuerto,
      //     'mes_actual' => $this->tiempoMuertoMes,
      //     'por_mes' => $this->tiemposMuertosPorMes,
      //     'ordenes_con_tiempo' => Orden::where('TiempoMuerto', '>', 0)->count()
      // ]);

    } catch (\Throwable $e) {
      Log::error('LivInicio::cargarTiemposMuertos - ' . $e->getMessage());
      $this->totalTiempoMuerto = 0;
      $this->tiempoMuertoMes = 0;
      $this->tiemposMuertosPorMes = [];
    }
  }

  private function cargarEmpleado($user)
  {
    try {
      $id = $user->IdUsuario;

      $estado = Orden::where('IdUsuario', $id)
        ->select(
          'Status',
          DB::raw('count(*) total')
        )
        ->groupBy('Status')
        ->pluck('total', 'Status');

      $this->misOrdenes = (int) array_sum($estado->toArray());
      $this->misOrdenesAbiertas = (int) ($estado['abierta'] ?? 0);
      $this->misOrdenesProceso = (int) ($estado['en_proceso'] ?? 0);
      $this->misOrdenesCerradas = (int) ($estado['cerrada'] ?? 0);
    } catch (\Throwable $e) {
      Log::error('LivInicio::cargarEmpleado - ' . $e->getMessage());
      $this->misOrdenes = 0;
      $this->misOrdenesAbiertas = 0;
      $this->misOrdenesProceso = 0;
      $this->misOrdenesCerradas = 0;
    }
  }

  private function inicializarGraficas()
  {
    if ($this->esVistaAdministrador()) {
      $this->estadoChartModel = $this->estadoChart();
      $this->areaChartModel = $this->areaChart();
      $this->mesChartModel = $this->mesChart();
      $this->tiemposMuertosChartModel = $this->tiemposMuertosChart();
    }
  }

  /*
    |--------------------------------------------------------------------------
    | PIE - Estado de órdenes
    |--------------------------------------------------------------------------
    */
  public function estadoChart()
  {
    $chart = (new PieChartModel())
      ->asDonut()
      ->setTitle('Distribución de órdenes');

    if ($this->ordenesAbiertas == 0 && $this->ordenesProceso == 0 && $this->ordenesCerradas == 0) {
      return $chart->addSlice('Sin datos', 1, '#e9ecef');
    }

    return $chart
      ->addSlice('Abiertas', $this->ordenesAbiertas, '#ffc107')
      ->addSlice('En proceso', $this->ordenesProceso, '#0dcaf0')
      ->addSlice('Cerradas', $this->ordenesCerradas, '#198754');
  }

  /*
    |--------------------------------------------------------------------------
    | COLUMN - Órdenes por área
    |--------------------------------------------------------------------------
    */
  public function areaChart()
  {
    $chart = (new ColumnChartModel())
      ->setDataLabelsEnabled(true)
      ->setTitle('Órdenes por área');

    try {
      $areas = Orden::join(
        'areas',
        'areas.IdArea',
        '=',
        'ordenes.IdArea'
      )
        ->select(
          'areas.Nombre',
          DB::raw('count(*) total')
        )
        ->groupBy('areas.Nombre')
        ->orderByDesc('total')
        ->limit(8)
        ->get();
    } catch (\Throwable $e) {
      Log::error('LivInicio::areaChart - ' . $e->getMessage());
      $areas = collect();
    }

    if ($areas->isEmpty()) {
      return $chart->addColumn('Sin datos', 1, '#e9ecef');
    }

    foreach ($areas as $area) {
      $chart->addColumn(
        $area->Nombre,
        $area->total,
        '#667eea'
      );
    }

    return $chart;
  }

  /*
    |--------------------------------------------------------------------------
    | LINE - Tendencia mensual de órdenes
    |--------------------------------------------------------------------------
    */
  public function mesChart()
  {
    $chart = (new LineChartModel())
      ->setTitle('Tendencia mensual de órdenes')
      ->setDataLabelsEnabled(true);

    try {
      for ($i = 11; $i >= 0; $i--) {
        $fecha = now()->subMonths($i);

        $total = Orden::whereYear('Timestamp', $fecha->year)
          ->whereMonth('Timestamp', $fecha->month)
          ->count();

        $chart->addPoint($fecha->format('M Y'), $total);
      }
    } catch (\Throwable $e) {
      Log::error('LivInicio::mesChart - ' . $e->getMessage());
    }

    return $chart;
  }

  /*
    |--------------------------------------------------------------------------
    | COLUMN - Tiempos muertos por mes
    |--------------------------------------------------------------------------
    */
  public function tiemposMuertosChart()
  {
    $chart = (new ColumnChartModel())
      ->setDataLabelsEnabled(true)
      ->setTitle('Tiempos muertos por mes (minutos)')
      ->setColors(['#dc3545']);

    if (empty($this->tiemposMuertosPorMes)) {
      return $chart->addColumn('Sin datos', 1, '#e9ecef');
    }

    foreach ($this->tiemposMuertosPorMes as $mes => $minutos) {
      $chart->addColumn($mes, $minutos, '#dc3545');
    }

    return $chart;
  }

  public function actualizarDashboard()
  {
    $this->cargando = true;
    $this->cargarDashboard();
    $this->inicializarGraficas();
    $this->cargando = false;
    $this->dispatch('dashboard-actualizado');
  }

  public function render()
  {
    return view('livewire.liv-inicio', [
      'estadoChart' => $this->estadoChartModel,
      'areaChart' => $this->areaChartModel,
      'mesChart' => $this->mesChartModel,
      'tiemposMuertosChart' => $this->tiemposMuertosChartModel,
      'esAdmin' => $this->esVistaAdministrador(),
    ]);
  }
}
