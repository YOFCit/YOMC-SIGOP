<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
  use HasFactory, Notifiable;

  // Tabla

  protected $table = 'empleados';

  // Primary Key personalizada

  protected $primaryKey = 'NumeroEmpleado';

  public $incrementing = false;

  protected $keyType = 'int';

  /**
   * Campos asignables
   */

  protected $fillable = [

    'NumeroEmpleado',
    'Email',
    'Nombre',
    'Position',
    'Departamento',
    'password'

  ];

  /**
   * Campos ocultos
   */

  protected $hidden = [

    'password',
    'remember_token'

  ];

  /**
   * Casts
   */

  protected $casts = [

    'password' => 'hashed',

  ];

  // RELACIONES

  public function ordenes()
  {
    return $this->hasMany(Orden::class, 'NumeroEmpleado');
  }
}
