<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProveedoresHasProductos extends Model
{
    use HasFactory;
    protected $table = 'proveedores_has_productos';
    protected $primarykey = 'proveedor_id';
    protected $fillable =['proveedor_id',
                          'producto_id'];
    public $timestamps = false;
    public function scopeProveedorId($query, $proveedorId)
    {
        if(!is_null($proveedorId))
        {
            return $query->where('proveedor_id', $proveedorId);
        }
        return $query;
    }
    public function scopeProductoId($query, $productoId)
    {
        if(!is_null($productoId))
        {
            return $query->where('producto_id', $productoId);
        }
        return $query;
    }
    // relaciones

    public function poveedor()
    {
        return $this->brlongsTo('App\Models\Proveedores', 'proveedor_id');
    }
    public function producto()
    {
        return $this->belongsTo('App\Models\Productos','producto_id');
    }
}
