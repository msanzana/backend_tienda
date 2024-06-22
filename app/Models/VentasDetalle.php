<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentasDetalle extends Model
{
    use HasFactory;
    protected $table = 'ventas_detalle';
    protected $primaryKey = 'id';
    protected $fillable = ['id',
                           'venta_id',
                           'producto_id',
                           'cantidad',
                           'total'];
    public $timestamps = false;
    public function scopeId($query, $id)
    {
        if(!is_null($id))
        {
            return $query->where('id',$id);
        }
        return $query;
    }
    public function scopeVentaId($query, $ventaId)
    {
        if(!is_null($ventaId))
        {
            return $query->where('venta_id', $ventaId);
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
    public function scopeCantidad($query, $cantidad)
    {
        if(!is_null($cantidad))
        {
            return $query->where('cantidad', $cantidad);
        }
        return $query;
    }
    public function scopeTotal($query, $total)
    {
        if(!is_null($total))
        {
            return $query->where('total', $total);
        }
        return $query;
    }
    // Relaciones
    public function producto()
    {
        return $this->blongsTo('App\Models\Productos','producto_id');
    }
    public function sucursal()
    {
        return $this->belongsTo('App\Models\Sucursales','sucursal_id');
    }
}
