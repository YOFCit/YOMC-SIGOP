<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
  protected $table = 'areas';

  protected $primaryKey = 'IdArea';

  protected $fillable = [
    'Nombre',
  ];

  /**
   * Un área tiene muchas líneas.
   */
  public function lineas()
  {
    return $this->hasMany(Linea::class, 'IdArea', 'IdArea');
  }
}
