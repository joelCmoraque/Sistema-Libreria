<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    protected $fillable = ['category_id', 'provider_id', 'deposit_id', 'nombre', 'descripcion', 'precio_actual', 'stock_actual', 'codigo_barra', 'created_at', 'updated_at'];

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
        return $this->belongsTo('App\Models\Category');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deposit()
    {
        return $this->belongsTo('App\Models\Deposit');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provider()
    {
        return $this->belongsTo('App\Models\Provider');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function historicals()
    {
        return $this->hasMany('App\Models\Historical');
    }
}
