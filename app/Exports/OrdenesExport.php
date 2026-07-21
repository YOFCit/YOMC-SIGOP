<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class OrdenesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles,  WithColumnFormatting
{
  protected $query;

  public function columnFormats(): array
  {
    return [
      'A' => NumberFormat::FORMAT_NUMBER,
    ];
  }

  public function __construct(Builder $query)
  {
    $this->query = $query;
  }

  public function query()
  {
    return $this->query;
  }

  public function headings(): array
  {
    return [
      'Folio',
      'Estado',
      'Tipo',
      'Área',
      'Línea',
      'Máquina',
      'Empleado',
      'Número Empleado',
      'Paro línea',
      'Tiempo muerto (min)',
      'Hora apertura',
      'Hora cierre',
      'Hora recepción línea',
      'Hora arranque',
      'Tiempo solución (con tiempo muerto)',
      'Tiempo espera (recepción - apertura)',
      'Tiempo arranque (arranque - recepción)',
      'Tiempo total (suma de todos)',
      'Descripción',
      'Procedimiento',
      'Descripción arranque',
      'Materiales'
    ];
  }

  public function map($orden): array
  {
    // Calcular tiempos en minutos
    $tiempoSolucionMinutos = $this->calcularTiempoSolucionMinutos($orden);
    $tiempoEsperaMinutos = $this->calcularTiempoEsperaMinutos($orden);
    $tiempoArranqueMinutos = $this->calcularTiempoArranqueMinutos($orden);
    $tiempoMuertoMinutos = (int)($orden->TiempoMuerto ?? 0);

    // Tiempo total = suma de todos los tiempos
    $tiempoTotalMinutos = $tiempoSolucionMinutos + $tiempoEsperaMinutos + $tiempoArranqueMinutos;

    // Formatear para mostrar
    $tiempoSolucion = $this->formatearTiempo($tiempoSolucionMinutos);
    $tiempoEspera = $this->formatearTiempo($tiempoEsperaMinutos);
    $tiempoArranque = $this->formatearTiempo($tiempoArranqueMinutos);
    $tiempoTotal = $this->formatearTiempo($tiempoTotalMinutos);

    return [
      (int) $orden->Folio,
      $orden->Status,
      ucfirst($orden->Tipo ?? 'correctivo'),
      $orden->area->Nombre ?? '',
      $orden->linea->Nombre ?? '',
      $orden->Maquina,
      $orden->empleado->Nombre ?? '',
      $orden->NumeroEmpleado,
      $orden->ParoLinea ? 'Sí' : 'No',
      $orden->TiempoMuerto ?? 0,
      optional($orden->HoraApertura)->format('d/m/Y H:i:s'),
      optional($orden->HoraCierre)->format('d/m/Y H:i:s'),
      optional($orden->HoraRecepcionLinea)->format('d/m/Y H:i:s'),
      optional($orden->HoraArranque)->format('d/m/Y H:i:s'),
      $tiempoSolucion,
      $tiempoEspera, // Ahora muestra correctamente: HoraApertura - HoraRecepcionLinea
      $tiempoArranque,
      $tiempoTotal,
      $orden->Descripcion,
      $orden->Procedimiento,
      $orden->DescripcionArranque,
      $orden->movimientos
        ->map(fn($m) => $m->material->Nombre . ' (' . $m->CantidadUsada . ')')
        ->implode(', ')
    ];
  }

  /**
   * Calcular tiempo de solución en minutos
   * (HoraSolución - HoraApertura) + TiempoMuerto
   */
  private function calcularTiempoSolucionMinutos($orden)
  {
    if (!$orden->HoraApertura || !$orden->TiempoSolucion) {
      return 0;
    }

    $inicio = Carbon::parse($orden->HoraApertura);
    $fin = Carbon::parse($orden->TiempoSolucion);

    if ($fin->lt($inicio)) {
      return 0;
    }

    $minutos = $inicio->diffInMinutes($fin);

    // Sumar tiempo muerto
    $minutos += (int)($orden->TiempoMuerto ?? 0);

    return $minutos;
  }

  /**
   * Calcular tiempo de espera en minutos
   * (HoraApertura - HoraRecepcionLinea) - SOLO EL TIEMPO DE ESPERA, SIN TIEMPO MUERTO
   */
  private function calcularTiempoEsperaMinutos($orden)
  {
    if (!$orden->HoraRecepcionLinea || !$orden->HoraApertura) {
      return 0;
    }

    $recepcion = Carbon::parse($orden->HoraRecepcionLinea);
    $apertura = Carbon::parse($orden->HoraApertura);

    // Si la recepción es menor que la apertura, no hay tiempo de espera
    if ($recepcion->lt($apertura)) {
      return 0;
    }

    // Diferencia en minutos entre recepción y apertura
    return $apertura->diffInMinutes($recepcion);
  }

  /**
   * Calcular tiempo de arranque en minutos
   * (HoraArranque - HoraRecepcionLinea)
   */
  private function calcularTiempoArranqueMinutos($orden)
  {
    if (!$orden->HoraRecepcionLinea || !$orden->HoraArranque) {
      return 0;
    }

    $recepcion = Carbon::parse($orden->HoraRecepcionLinea);
    $arranque = Carbon::parse($orden->HoraArranque);

    if ($arranque->lt($recepcion)) {
      return 0;
    }
    return $recepcion->diffInMinutes($arranque);
  }

  /**
   * Formatear minutos en un string legible
   */
  private function formatearTiempo($minutos)
  {
    if ($minutos <= 0) {
      return '0 minutos';
    }

    $dias = intdiv($minutos, 1440);
    $minutos %= 1440;
    $horas = intdiv($minutos, 60);
    $minutos %= 60;

    $partes = [];
    if ($dias > 0) {
      $partes[] = $dias . ' día' . ($dias > 1 ? 's' : '');
    }
    if ($horas > 0) {
      $partes[] = $horas . ' hora' . ($horas > 1 ? 's' : '');
    }
    if ($minutos > 0) {
      $partes[] = $minutos . ' minuto' . ($minutos > 1 ? 's' : '');
    }

    return empty($partes) ? '0 minutos' : implode(', ', $partes);
  }

  public function styles(Worksheet $sheet)
  {
    return [
      1 => [
        'font' => ['bold' => true, 'size' => 11],
        'fill' => [
          'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
          'startColor' => ['rgb' => 'E8F4FD']
        ],
        'alignment' => ['horizontal' => 'center']
      ],
    ];
  }
}
