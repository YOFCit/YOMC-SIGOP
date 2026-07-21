<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tiemposmuertos extends Model
{
  use HasFactory;

  protected $table = 'tiempos_muertos'; // Ajusta si es necesario

  protected $fillable = [
    'Name',
    'EmployeeID',
    'Area',
    'ProductionLine',
    'Description',
    'Departament',
    'DateOfOpen',
    'Status',
    'SolutionDescription',
    'DateOfClose',
    'TimeUsed',
    'created_by',
    'user_departament',
  ];

  protected $casts = [
    'DateOfOpen' => 'datetime',
    'DateOfClose' => 'datetime',
  ];
}
