<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\User;

class Orden extends Model
{
  protected $table = 'ordenes';
  protected $primaryKey = 'IdOrden';

  protected $fillable = [
    'Folio',
    'Descripcion',
    'IdArea',
    'IdLinea',
    'NumeroEmpleado',
    'Maquina',
    'Timestamp',
    'HoraApertura',
    'HoraCierre',
    'Status',
    'Tipo',
    'Otro',
    'Procedimiento',
    'ParoLinea',
    'TiempoMuerto',
    'TiempoSolucion',
    'ReqMaterial',
    // NUEVOS CAMPOS
    'HoraRecepcionLinea',
    'HoraArranque',
    'DescripcionArranque',
    'Engineer'
  ];

  protected $casts = [
    'ParoLinea' => 'boolean',
    'ReqMaterial' => 'boolean',
    'Timestamp' => 'datetime',
    'HoraApertura' => 'datetime',
    'HoraCierre' => 'datetime',
    'HoraRecepcionLinea' => 'datetime', // NUEVO
    'HoraArranque' => 'datetime'        // NUEVO
  ];

  // Relaciones
  public function empleado()
  {
    return $this->belongsTo(User::class, 'NumeroEmpleado', 'NumeroEmpleado');
  }

  public function area()
  {
    return $this->belongsTo(Area::class, 'IdArea', 'IdArea');
  }

  public function linea()
  {
    return $this->belongsTo(Linea::class, 'IdLinea', 'IdLinea');
  }

  public function movimientos()
  {
    return $this->hasMany(Movimiento::class, 'IdOrden', 'IdOrden');
  }

  // Accessors para nombres
  public function getAreaNombreAttribute()
  {
    return $this->area ? $this->area->Nombre : 'N/A';
  }

  public function getLineaNombreAttribute()
  {
    return $this->linea ? $this->linea->Nombre : 'N/A';
  }

  public function getEmpleadoNombreAttribute()
  {
    return $this->empleado ? $this->empleado->Nombre : 'N/A';
  }

  // NUEVO: Calcular tiempo de espera (recepción - apertura)
  public function getTiempoEsperaAttribute()
  {
    if (!$this->HoraRecepcionLinea || !$this->HoraApertura) {
      return 'N/A';
    }

    $diff = $this->HoraRecepcionLinea->diff($this->HoraApertura);

    if ($diff->h > 0) {
      return "{$diff->h}h {$diff->i}m";
    } elseif ($diff->i > 0) {
      return "{$diff->i} minutos";
    } else {
      return "{$diff->s} segundos";
    }
  }

  // NUEVO: Calcular tiempo de arranque (arranque - recepción)
  public function getTiempoArranqueAttribute()
  {
    if (!$this->HoraArranque || !$this->HoraRecepcionLinea) {
      return 'N/A';
    }

    $diff = $this->HoraRecepcionLinea->diff($this->HoraArranque);

    if ($diff->h > 0) {
      return "{$diff->h}h {$diff->i}m";
    } elseif ($diff->i > 0) {
      return "{$diff->i} minutos";
    } else {
      return "{$diff->s} segundos";
    }
  }

  // NUEVO: Calcular tiempo total de la orden (cierre - apertura)
  public function getTiempoTotalAttribute()
  {
    if (!$this->HoraApertura) return 'N/A';

    $fin = $this->HoraCierre ?? Carbon::now();
    $diff = $this->HoraApertura->diff($fin);

    if ($diff->h > 0) {
      return "{$diff->h}h {$diff->i}m";
    } elseif ($diff->i > 0) {
      return "{$diff->i} minutos";
    } else {
      return "{$diff->s} segundos";
    }
  }

  public function getStatusBadgeAttribute()
  {
    return match ($this->Status) {
      'cerrada' => '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Cerrada</span>',
      'en_proceso' => '<span class="badge bg-info"><i class="fas fa-spinner fa-pulse"></i> En proceso</span>',
      default => '<span class="badge bg-warning text-dark"><i class="fas fa-play"></i> Abierta</span>'
    };
  }

  public function getParoInfoAttribute()
  {
    if (!$this->ParoLinea) {
      return '<span class="text-muted">Sin paro</span>';
    }

    return "<span class=\"badge bg-danger\"><i class=\"fas fa-stop-circle\"></i> {$this->TiempoMuerto} min</span>
                <small class=\"text-muted d-block\">Solución: {$this->TiempoSolucion} min</small>";
  }
}
