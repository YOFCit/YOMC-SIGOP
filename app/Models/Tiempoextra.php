<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tiempoextra extends Model
{
  use HasFactory;
  protected $table = 'tiempos_extra';

  protected $fillable = [
    'NumeroEmpleado',
    'Nombre',
    'Departamento',
    'TrabajarA',
    'HoraInicio',
    'HoraFin',
    'HorasExtra',
    'Descripcion',
    'Causas',
    'Solicitante',
    'FechaSolicitud',
    'Autorizador',
    'Estatus',
  ];
}
