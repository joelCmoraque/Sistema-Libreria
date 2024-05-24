<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $product_id
 * @property integer $provider_id
 * @property integer $cantidad
 * @property string $fecha
 * @property float $precio_unitario
 * @property string $documento_referencia
 * @property string $created_at
 * @property string $updated_at
 * @property Product $product
 * @property Provider $provider
 */
class Input extends Model
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
    protected $fillable = ['product_id', 'provider_id', 'cantidad', 'fecha', 'precio_unitario', 'documento_referencia', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provider()
    {
        return $this->belongsTo('App\Models\Provider');
    }
}
