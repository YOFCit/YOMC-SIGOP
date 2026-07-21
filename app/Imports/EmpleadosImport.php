<?php

namespace App\Imports;

use App\Models\Empleados;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class EmpleadosImport implements ToCollection
{
  public function collection(Collection $rows)
  {
    // Elimina encabezado
    $rows->shift();

    foreach ($rows as $row) {

      if (empty($row[0]) || empty($row[1])) {
        continue;
      }

      Empleados::updateOrCreate(
        [
          'NumeroEmpleado' => trim($row[0]),
        ],
        [
          'Nombre' => trim($row[1]),
        ]
      );
    }
  }
}
