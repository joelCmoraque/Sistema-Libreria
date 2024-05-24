<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $product_id
 * @property float $precio_antiguo
 * @property float $precio_nuevo
 * @property string $fecha_cambio
 * @property string $created_at
 * @property string $updated_at
 * @property Product $product
 */
class Historical extends Model
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
    protected $fillable = ['product_id', 'precio_antiguo', 'precio_nuevo', 'fecha_cambio', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }
}
