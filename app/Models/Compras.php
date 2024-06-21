<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compras extends Model
{
    use HasFactory;
    protected $table = "compras";
    protected $primaryKey = 'id';
    protected $fillable = ['id',
                           'fecha',
                           'trabajador_id',
                           'proveedor_id',
                           'sucursal_id',
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
    public function scopeFecha($query, $fecha)
    {
        if(!is_null($fecha))
        {
            return $query->where('fecha', $fecha);
        }
        return $query;
    }
    public function scopeTrabajadorId($query, $trabajadorId)
    {
        if(!is_null($trabajadorId))
        {
            return $query->where('trabajador_id', $trabajadorId);
        }
        return $query;
    }
    public function scopeSucursalId($query, $sucursalId)
    {
        if(!is_null($sucursalId))
        {
            return $query->where('sucursal_id',$sucursalId);
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
    public function scopeProveedorId($query, $proveedorId)
    {
        if(!is_null($proveedorId))
        {
            return $query->where('proveedor_id', $proveedorId);
        }
        return $query;
    }
    // relaciones
    public function trabajador()
    {
        return $this->belongsTo('App\Models\Trabajadores','trabajador_id');
    }
    public function sucursal()
    {
        return $this->belongsTo('App\Models\Sucursales','sucursal_id');
    }
    public function proveedor()
    {
        return $this->belongsTo('App\Models\Proveedores','proveedor_id');
    }
    public function detalle()
    {
        return $this->hasMany('App\Models\ComprasDetalle','compra_id','id');
    }
}
