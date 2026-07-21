<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Linea extends Model
{
  protected $table = 'lineas';

  protected $primaryKey = 'IdLinea';

  protected $fillable = [
    'Nombre',
    'IdArea',
  ];

  /**
   * Una línea pertenece a un área.
   */
  public function area()
  {
    return $this->belongsTo(Area::class, 'IdArea', 'IdArea');
  }

  /**
   * Una línea tiene muchas máquinas.
   */
  public function maquinas()
  {
    return $this->hasMany(Maquina::class, 'IdLinea', 'IdLinea');
  }
}
