<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

/**
 * @property integer $id
 * @property integer $category_id
 * @property integer $provider_id
 * @property integer $deposit_id
 * @property string $nombre
 * @property string $descripcion
 * @property float $precio_actual
 * @property integer $stock_actual
 * @property string $codigo_barra
 * @property string $created_at
 * @property string $updated_at
 * @property Output[] $outputs
 * @property Input[] $inputs
 * @property Category $category
 * @property Deposit $deposit
 * @property Provider $provider
 * @property Historical[] $historicals
 */
class Product extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['category_id', 'provider_id', 'deposit_id', 'nombre', 'descripcion', 'precio_actual', 'stock_actual',  'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function outputs()
    {
        return $this->hasMany('App\Models\Output');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inputs()
    {
        return $this->hasMany('App\Models\Input');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        
        return $this->belongsTo(Category::class,'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deposit()
    {
        
        return $this->belongsTo(Deposit::class,'deposit_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provider()
    {
      
        return $this->belongsTo(Provider::class,'provider_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function historicals()
    {
        return $this->hasMany('App\Models\Historical');
    }

    // Evento de modelo para generar el código único antes de crear el producto
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->codigo_unico = self::generateUniqueCode($product->nombre);
        });
    }

    // Método para generar el código único basado en las iniciales del nombre del producto
    protected static function generateUniqueCode($nombre)
    {
        $initials = self::getInitials($nombre);
        $number = 1;
        $codeLength = 6;
        
        // Calcula la longitud de las iniciales y el número secuencial
        $initialsLength = strlen($initials);
        $numberLength = strlen((string)$number);
    
        // Calcula cuántos ceros necesitas para llenar el código
        $zeroPadding = max(0, $codeLength - $initialsLength - $numberLength);
    
        // Rellena con ceros a la izquierda si es necesario
        $code = strtoupper($initials . str_repeat('0', $zeroPadding) . $number);
    
        // Verifica si el código generado ya existe en la base de datos
        while (self::where('codigo_unico', $code)->exists()) {
            $number++;
            $numberLength = strlen((string)$number);
            $zeroPadding = max(0, $codeLength - $initialsLength - $numberLength);
            $code = strtoupper($initials . str_repeat('0', $zeroPadding) . $number);
        }
    
        return $code;
    }

    // Método para obtener las iniciales del nombre
    protected static function getInitials($nombre)
    {
        $words = explode(' ', $nombre);
        $initials = '';
        foreach ($words as $word) {
            $initials .= substr($word, 0, 1);
        }
        return $initials;
    }
}
