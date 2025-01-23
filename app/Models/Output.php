<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $product_id
 * @property integer $cantidad
 * @property string $fecha
 * @property string $documento_referencia
 * @property string $created_at
 * @property string $updated_at
 * @property Product $product
 */
class Output extends Model
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
    protected $fillable = ['product_id', 'cantidad','precio_unitario','total', 'fecha_salida', 'documento_referencia', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }
}
