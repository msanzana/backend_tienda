<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComprasDetalle extends Model
{
    use HasFactory;
    protected $table = 'compras_detalle';
    protected $primaryKey = 'id';
    protected $fillable = ['id',
                           'compra_id',
                           'producto_id',
                           'cantidad',
                           'total'];
    public $timestamps = false;
    public function scopeId($query, $id)
    {
        if(!is_null($id))
        {
            return $query->where('id', $id);
        }
        return $query;
    }
    public function scopeCompraId($query, $compraId)
    {
        if(!is_null($compraId))
        {
            return $query->where('compra_id', $compraId);
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
    //relaciones
    public function producto()
    {
        return $this->belongsTo('App\Models\Productos','producto_id');
    }
}
