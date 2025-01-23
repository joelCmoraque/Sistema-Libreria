<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DimTime extends Model
{
    use HasFactory;

    protected $connection ="pgsql_second";

      // Especifica el nombre de la clave primaria
      protected $primaryKey = 'time_key';

      // Desactiva el incremento automático si no es un campo autoincrementable
      public $incrementing = false;
  
      // Si la clave primaria no es de tipo integer, debes especificar su tipo
      protected $keyType = 'bigint';

    protected $fillable = [
        'fecha',
        'año',
        'mes',
        'dia',
        'trimestre',
        'dia_semana',
        'semana_año',
    ];
}
