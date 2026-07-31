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
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class OrdenesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting, WithColumnWidths
{
  protected $query;

  public function columnFormats(): array
  {
    return [];
  }

  public function columnWidths(): array
  {
    return [
      'A' => 20,  // Folio
      'B' => 15,  // Estado
      'C' => 15,  // Tipo
      'D' => 18,  // Área
      'E' => 18,  // Línea
      'F' => 18,  // Máquina
      'G' => 22,  // Empleado
      'H' => 18,  // Número Empleado
      'I' => 15,  // Paro línea
      'J' => 22,  // Hora apertura
      'K' => 22,  // Hora cierre
      'L' => 22,  // Hora recepción línea
      'M' => 22,  // Hora arranque
      'N' => 20,  // Tiempo solución
      'O' => 25,  // Tiempo espera
      'P' => 25,  // Tiempo arranque
      'Q' => 22,  // Tiempo total
      'R' => 45,  // Descripción
      'S' => 45,  // Procedimiento
      'T' => 45,  // Descripción arranque
      'U' => 50,  // Materiales
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
      'Hora apertura',
      'Hora cierre',
      'Hora recepción línea',
      'Hora arranque',
      'Tiempo solución',
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

    /*
         * Tiempo total:
         *
         * Tiempo solución + Tiempo espera + Tiempo arranque
         *
         * Ya no se descuenta TiempoMuerto porque ese campo
         * ya no forma parte del reporte.
         */
    $tiempoTotalMinutos =
      $tiempoSolucionMinutos +
      $tiempoEsperaMinutos +
      $tiempoArranqueMinutos;

    return [
      (int) $orden->Folio,
      $orden->Status,
      ucfirst($orden->Tipo ?? 'correctivo'),
      $orden->area->Nombre ?? '',
      $orden->linea->Nombre ?? '',
      $orden->Maquina,
      $orden->empleado->Nombre ?? '',
      $orden->NumeroEmpleado,

      // ParoLinea ahora es únicamente Sí / No
      $orden->ParoLinea ? 'Sí' : 'No',

      optional($orden->HoraApertura)->format('d/m/Y H:i:s'),
      optional($orden->HoraCierre)->format('d/m/Y H:i:s'),
      optional($orden->HoraRecepcionLinea)->format('d/m/Y H:i:s'),
      optional($orden->HoraArranque)->format('d/m/Y H:i:s'),

      $this->formatearTiempo($tiempoSolucionMinutos),
      $this->formatearTiempo($tiempoEsperaMinutos),
      $this->formatearTiempo($tiempoArranqueMinutos),
      $this->formatearTiempo($tiempoTotalMinutos),

      $orden->Descripcion ?? '',
      $orden->Procedimiento ?? '',
      $orden->DescripcionArranque ?? '',

      $orden->movimientos
        ->map(fn($m) => $m->material->Nombre . ' (' . $m->CantidadUsada . ')')
        ->implode(', ')
    ];
  }

  /**
   * Calcular tiempo de solución en minutos
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

    return $inicio->diffInMinutes($fin);
  }

  /**
   * Calcular tiempo de espera en minutos
   */
  private function calcularTiempoEsperaMinutos($orden)
  {
    if (!$orden->HoraRecepcionLinea || !$orden->HoraApertura) {
      return 0;
    }

    $recepcion = Carbon::parse($orden->HoraRecepcionLinea);
    $apertura = Carbon::parse($orden->HoraApertura);

    if ($recepcion->lt($apertura)) {
      return 0;
    }

    return $apertura->diffInMinutes($recepcion);
  }

  /**
   * Calcular tiempo de arranque en minutos
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

    return empty($partes)
      ? '0 minutos'
      : implode(', ', $partes);
  }

  public function styles(Worksheet $sheet)
  {
    $highestRow = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();

    // =====================
    // 1. ENCABEZADOS
    // =====================

    $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
      'font' => [
        'bold' => true,
        'size' => 16,
        'name' => '宋体',
        'color' => ['rgb' => '000000']
      ],
      'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'C0C0C0']
      ],
      'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true
      ],
      'borders' => [
        'allBorders' => [
          'borderStyle' => Border::BORDER_THIN,
          'color' => ['rgb' => '000000']
        ]
      ]
    ]);

    // =====================
    // 2. DATOS
    // =====================

    $sheet->getStyle('A2:' . $highestColumn . $highestRow)->applyFromArray([
      'font' => [
        'name' => 'Calibri',
        'size' => 12
      ],
      'alignment' => [
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true
      ]
    ]);

    // =====================
    // 3. ALINEACIONES
    // =====================

    // Folio
    $sheet->getStyle('A2:A' . $highestRow)->applyFromArray([
      'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_RIGHT
      ]
    ]);

    // Estado, Tipo y Paro línea
    $sheet->getStyle('B2:B' . $highestRow)->applyFromArray([
      'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER
      ]
    ]);

    $sheet->getStyle('C2:C' . $highestRow)->applyFromArray([
      'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER
      ]
    ]);

    $sheet->getStyle('I2:I' . $highestRow)->applyFromArray([
      'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER
      ]
    ]);

    // Fechas
    $sheet->getStyle('J2:M' . $highestRow)->applyFromArray([
      'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER
      ]
    ]);

    // Tiempos
    $sheet->getStyle('N2:Q' . $highestRow)->applyFromArray([
      'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_LEFT
      ]
    ]);

    // Descripciones y materiales
    $sheet->getStyle('R2:U' . $highestRow)->applyFromArray([
      'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_LEFT,
        'vertical' => Alignment::VERTICAL_TOP,
        'wrapText' => true
      ]
    ]);

    // =====================
    // 4. ALTURA DE FILAS
    // =====================

    $sheet->getRowDimension(1)->setRowHeight(60);

    for ($row = 2; $row <= $highestRow; $row++) {
      $sheet->getRowDimension($row)->setRowHeight(-1);
    }

    // =====================
    // 5. ANCHO DE COLUMNAS
    // =====================

    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(18);
    $sheet->getColumnDimension('C')->setWidth(18);
    $sheet->getColumnDimension('D')->setWidth(22);
    $sheet->getColumnDimension('E')->setWidth(22);
    $sheet->getColumnDimension('F')->setWidth(22);
    $sheet->getColumnDimension('G')->setWidth(28);
    $sheet->getColumnDimension('H')->setWidth(22);
    $sheet->getColumnDimension('I')->setWidth(18);
    $sheet->getColumnDimension('J')->setWidth(26);
    $sheet->getColumnDimension('K')->setWidth(26);
    $sheet->getColumnDimension('L')->setWidth(26);
    $sheet->getColumnDimension('M')->setWidth(26);
    $sheet->getColumnDimension('N')->setWidth(24);
    $sheet->getColumnDimension('O')->setWidth(30);
    $sheet->getColumnDimension('P')->setWidth(30);
    $sheet->getColumnDimension('Q')->setWidth(26);
    $sheet->getColumnDimension('R')->setWidth(50);
    $sheet->getColumnDimension('S')->setWidth(50);
    $sheet->getColumnDimension('T')->setWidth(50);
    $sheet->getColumnDimension('U')->setWidth(55);

    // =====================
    // 6. CONGELAR PANEL
    // =====================

    $sheet->freezePane('A2');

    // =====================
    // 7. FORMATO DE FOLIO
    // =====================

    $sheet->getStyle('A2:A' . $highestRow)
      ->getNumberFormat()
      ->setFormatCode('0');
  }
}
