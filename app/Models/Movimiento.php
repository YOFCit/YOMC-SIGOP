<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
  protected $table = 'movimientos';

  protected $primaryKey = 'IdMovimiento';

  protected $fillable = [
    'TipoMovimiento',
    'CantidadUsada',
    'IdOrden',
    'IdMaterial'
  ];

  public function orden()
  {
    return $this->belongsTo(Orden::class, 'IdOrden');
  }

  public function material()
  {
    return $this->belongsTo(Material::class, 'IdMaterial');
  }
}
