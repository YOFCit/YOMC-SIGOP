<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
  protected $table = 'materiales';

  protected $primaryKey = 'IdMaterial';

  protected $fillable = [
    'Nombre',
    'Descripcion',
    'Location',
    'Stock'
  ];

  // RELACIONES

  public function movimientos()
  {
    return $this->hasMany(Movimiento::class, 'IdMaterial');
  }
}
