<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DimProducts extends Model
{
    use HasFactory;

    protected $connection ="pgsql_second";

      // Especifica el nombre de la clave primaria
      protected $primaryKey = 'product_key';

      // Desactiva el incremento automático si no es un campo autoincrementable
      public $incrementing = false;
  
      // Si la clave primaria no es de tipo integer, debes especificar su tipo
      protected $keyType = 'bigint';
  
      protected $fillable = [
          'product_id',
          'codigo_unico',
          'nombre',
          'descripcion',
          'unidad_medida',
          'category_id',
          'category_nombre',
          'brand_id',
          'brand_nombre',
          'provider_id',
          'provider_nombre',
      ];
}
