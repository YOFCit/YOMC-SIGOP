<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Area;
use App\Models\Linea;
use App\Models\Maquina;

class MaquinasSeeder extends Seeder
{
  public function run()
  {
    $estructura = [
      'Coloring' => [
        'CL01' => ['Payoff', 'Takeup', 'Poleas', 'Molde', 'Lampara', 'Nitrogeno', 'Deposito tinta'],
        'CL02' => ['Payoff', 'Takeup', 'Poleas', 'Molde', 'Lampara', 'Nitrogeno', 'Deposito tinta'],
        'CL03' => ['Payoff', 'Takeup', 'Poleas', 'Molde', 'Lampara', 'Nitrogeno', 'Deposito tinta'],
        'CL04' => ['Payoff', 'Takeup', 'Poleas', 'Molde', 'Lampara', 'Nitrogeno', 'Deposito tinta'],
      ],
      'Indoor FH' => [
        'FH01' => [
          'Payoff Acero',
          'Payoff Fibra',
          'Payoff GRP',
          'Servidor de Aramidas',
          'Sistema secador de Compuesto',
          'Extrusora',
          'Canaletas',
          'Medidor de Diametro',
          'Medidor de Grumos',
          'Capstan',
          'Acumulador',
          'Takeup'
        ],
        'FH02' => [
          'Payoff Acero',
          'Payoff Fibra',
          'Payoff GRP',
          'Servidor de Aramidas',
          'Sistema secador de Compuesto',
          'Extrusora',
          'Canaletas',
          'Medidor de Diametro',
          'Medidor de Grumos',
          'Capstan',
          'Acumulador',
          'Takeup'
        ],
        'FH03' => [
          'Payoff Acero',
          'Payoff Fibra',
          'Payoff GRP',
          'Servidor de Aramidas',
          'Sistema secador de Compuesto',
          'Extrusora',
          'Canaletas',
          'Medidor de Diametro',
          'Medidor de Grumos',
          'Capstan',
          'Acumulador',
          'Takeup'
        ],
        'FH04' => [
          'Payoff Acero',
          'Payoff Fibra',
          'Payoff GRP',
          'Servidor de Aramidas',
          'Sistema secador de Compuesto',
          'Extrusora',
          'Canaletas',
          'Medidor de Diametro',
          'Medidor de Grumos',
          'Capstan',
          'Acumulador',
          'Takeup'
        ],
      ],
      'Indoor TB' => [
        'TB01' => [
          'Payoff Fibra',
          'Precalentador',
          'Bomba de Vacio',
          'Tolva',
          'Sistema secador de Compuesto',
          'Extrusora',
          'Canaletas',
          'Medidor de Diametro',
          'Medidor de Grumos',
          'Takeup'
        ],
        'TB02' => [
          'Payoff Fibra',
          'Precalentador',
          'Bomba de Vacio',
          'Tolva',
          'Sistema secador de Compuesto',
          'Extrusora',
          'Canaletas',
          'Medidor de Diametro',
          'Medidor de Grumos',
          'Takeup'
        ],
      ],
      'RWD' => [
        'RWD' => [
          'Payoff Cable',
          'Takeup',
          'Caterpillar',
          'Dancer',
          'Impresora de Inyeccion',
          'Impresora Mecanica'
        ],
      ],
      'Facilities' => [
        'Facilities' => [
          'Otros'
        ],
      ],
      'Secondary Coating' => [
        'SC01' => [
          'Payoff de Fibra',
          'Ionizador',
          'Dispensado de Gel',
          'Sistema secador de Compuesto',
          'Sistema de Gel',
          'Extrusora',
          'Clenching',
          'Canaletas',
          'Capstan',
          'Medidor de Diametro',
          'Medidor de Grumos',
          'Secador',
          'Dancer',
          'Takeup'
        ],
        'SC02' => [
          'Payoff de Fibra',
          'Ionizador',
          'Dispensado de Gel',
          'Sistema secador de Compuesto',
          'Sistema de Gel',
          'Extrusora',
          'Clenching',
          'Canaletas',
          'Capstan',
          'Medidor de Diametro',
          'Medidor de Grumos',
          'Secador',
          'Dancer',
          'Takeup'
        ],
        'SC03' => [
          'Payoff de Fibra',
          'Ionizador',
          'Dispensado de Gel',
          'Sistema secador de Compuesto',
          'Sistema de Gel',
          'Extrusora',
          'Clenching',
          'Canaletas',
          'Capstan',
          'Medidor de Diametro',
          'Medidor de Grumos',
          'Secador',
          'Dancer',
          'Takeup'
        ],
      ],
      'Sheating' => [
        'SH01' => [
          'Payoff Core',
          'Dancer 1',
          'Servidor de aramidas',
          'SZ-SHO1',
          'Binder Unit',
          'Sistema secador de compuesto',
          'Extrusora',
          'Canaletas',
          'Secador',
          'Medidor de diametro',
          'Impresora laser',
          'Impresora mecanica',
          'Impresora de inyeccion',
          'Caterpillar',
          'Dancer',
          'Takeup'
        ],
        'SH02' => [
          'Payoff Core',
          'Dancer 1',
          'Servidor de aramidas',
          'SZ-SHO1',
          'Binder Unit',
          'Sistema secador de compuesto',
          'Extrusora',
          'Canaletas',
          'Secador',
          'Medidor de diametro',
          'Impresora laser',
          'Impresora mecanica',
          'Impresora de inyeccion',
          'Caterpillar',
          'Dancer',
          'Takeup'
        ],
        'SH03' => [
          'Payoff Core',
          'Dancer 1',
          'Servidor de aramidas',
          'SZ-SHO1',
          'Binder Unit',
          'Sistema secador de compuesto',
          'Extrusora',
          'Canaletas',
          'Secador',
          'Medidor de diametro',
          'Impresora laser',
          'Impresora mecanica',
          'Impresora de inyeccion',
          'Caterpillar',
          'Dancer',
          'Takeup'
        ],
        'SH04' => [
          'Payoff Core',
          'Dancer 1',
          'Servidor de aramidas',
          'SZ-SHO1',
          'Binder Unit',
          'Sistema secador de compuesto',
          'Extrusora',
          'Canaletas',
          'Secador',
          'Medidor de diametro',
          'Impresora laser',
          'Impresora mecanica',
          'Impresora de inyeccion',
          'Caterpillar',
          'Dancer',
          'Takeup'
        ],
        'SH05' => [
          'Payoff Core',
          'Dancer 1',
          'Servidor de aramidas',
          'SZ-SHO1',
          'Binder Unit',
          'Sistema secador de compuesto',
          'Extrusora',
          'Canaletas',
          'Secador',
          'Medidor de diametro',
          'Impresora laser',
          'Impresora mecanica',
          'Impresora de inyeccion',
          'Caterpillar',
          'Dancer',
          'Takeup'
        ],
      ],
      'Stranding' => [
        'SZ01' => [
          'Payoff de FRP',
          'Payoffs Loose Tube',
          'CMS Dancer',
          'SZ',
          'Binder Unit',
          'Sistema de WBT',
          'Capstan',
          'Dancer',
          'Takeup'
        ],
        'SZ02' => [
          'Payoff de FRP',
          'Payoffs Loose Tube',
          'CMS Dancer',
          'SZ',
          'Binder Unit',
          'Sistema de WBT',
          'Capstan',
          'Dancer',
          'Takeup'
        ],
        'SZ03' => [
          'Payoff de FRP',
          'Payoffs Loose Tube',
          'CMS Dancer',
          'SZ',
          'Binder Unit',
          'Sistema de WBT',
          'Capstan',
          'Dancer',
          'Takeup'
        ],
      ],
    ];

    // Insertar todo
    foreach ($estructura as $nombreArea => $lineas) {
      // Buscar o crear el área
      $area = Area::firstOrCreate(['Nombre' => $nombreArea]);

      foreach ($lineas as $nombreLinea => $maquinas) {
        // Buscar o crear la línea
        $linea = Linea::firstOrCreate([
          'Nombre' => $nombreLinea,
          'IdArea' => $area->IdArea
        ]);

        foreach ($maquinas as $nombreMaquina) {
          // Buscar o crear la máquina
          Maquina::firstOrCreate([
            'Nombre' => $nombreMaquina,
            'IdLinea' => $linea->IdLinea
          ]);
        }
      }
    }

    $this->command->info('✅ Estructura de máquinas completada exitosamente');
  }
}
