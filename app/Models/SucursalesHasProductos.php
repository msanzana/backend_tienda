<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SucursalesHasProductos extends Model
{
    use HasFactory;
    protected $table = 'sucursales_has_productos';
    protected $primaryKey = 'id';
    protected $fillable =['id',
                          'sucursal_id',
                          'producto_id',
                          'stock'];
    public $timestamps = false;
    public function scopeId($query, $id)
    {
        if(!is_null($id))
        {
            return $query->where('id', $id);
        }
        return $query;
    }
    public function scopeSucursalId($query, $sucursalId)
    {
        if(!is_null($sucursalId))
        {
            return $query->where('sucursal_id', $sucursalId);
        }
        return $query;
    }
    public function scopeProductoId($query, $productoId)
    {
        if(!is_null($productoId))
        {
            return $query->where('poducto_id', $productoId);
        }
        return $query;
    }
    public function scopeStock($query, $stock)
    {
        if(!is_null($stock))
        {
            return $query->where('stock',$stock);
        }
        return $query;
    }
    // relaciones
    public function sucursal()
    {
        return $this->belongsTo('App\Models\Sucursales','sucursal_id');
    }
    public function producto()
    {
        return $this->belongsTo('App\Models\Productos','producto_id');
    }
}
