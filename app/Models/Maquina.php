<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maquina extends Model
{
  use HasFactory;

  protected $table = 'maquinas';

  protected $primaryKey = 'IdMaquina';

  protected $fillable = [
    'Nombre',
    'IdLinea',
  ];

  /**
   * Una máquina pertenece a una línea.
   */
  public function linea()
  {
    return $this->belongsTo(Linea::class, 'IdLinea', 'IdLinea');
  }

  /**
   * Acceso directo al área a través de la línea.
   */
  public function area()
  {
    return $this->hasOneThrough(
      Area::class,      // Modelo final
      Linea::class,     // Modelo intermedio
      'IdLinea',        // FK en lineas relacionada con maquinas
      'IdArea',         // PK en areas
      'IdLinea',        // FK local en maquinas
      'IdArea'          // FK en lineas hacia areas
    );
  }
}
