<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactTransactions extends Model
{
    use HasFactory;

    protected $connection ="pgsql_second";

    // Especifica el nombre de la clave primaria
    protected $primaryKey = 'transaction_id';

    // Desactiva el incremento automático si no es un campo autoincrementable
    public $incrementing = false;

    // Si la clave primaria no es de tipo integer, debes especificar su tipo
    protected $keyType = 'bigint';

    protected $fillable = [
        'product_key',
        'deposit_key',
        'time_key',
        'quantity',
        'unit_price',
        'total_price',
        'transaction_type',
        'reference_document',
    ];
}
