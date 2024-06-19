<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    use HasFactory;
    protected $table = 'productos';
    protected $primaryKey = 'id';
    protected $fillable = ['id',
                           'nombre',
                           'precio_venta'];
    public $timestamps = false;
    public function scopeId($query, $id)
    {
        if(!is_null($id))
        {
            return $query->where('id',$id);
        }
        return $query;
    }
    public function scopeNombre($query, $nombre)
    {
        if(!is_null($nombre))
        {
            return $query->where('nombre','LIKE','%'.$nombre.'%');
        }
        return $query;
    }
    public function scopePrecioVenta($query, $precioVenta)
    {
        if(!is_null($precioVenta))
        {
            return $query->where('precio_venta',$precioVenta);
        }
        return $query;
    }
    public function scopeActivo($query, $activo)
    {
        if(!is_null($activo))
        {
            return $query->where('activo',$activo);
        }
        return $query;
    }
    public function scopeSucursalId($query, $sucursalId)
    {
        if(!is_null($sucursalId))
        {
            return $query->where('sucu.id','=', $sucursalId)
                        ->join('sucursales_has_productos AS suap','suap.producto_id','productos.id')
                        ->join('sucursales AS sucu', 'sucu.id','=','suap.sucursal_id')
                        ->select('productos.id',
                                 'productos.nombre',
                                 'productos.precio_venta',
                                 'productos.activo');

        }
        return $query;
    }
    public function scopeProveedorId($query, $proveedorId)
    {
        if(!is_null($proveedorId))
        {
            return $query->where('prov.id','=', $proveedorId)
                        ->join('proveedores_has_productos AS prvp','prvp.producto_id','productos.id')
                        ->join('proveedores AS prov', 'prov.id','=','prvp.proveedor_id')
                        ->select('productos.id',
                                 'productos.nombre',
                                 'productos.precio_venta',
                                 'productos.activo');
            
        }
        return $query;
    }
    public function proveedoresHasProductos()
    {
        return $this->belongsToMany('App\Models\Proveedores','proveedores_has_productos','producto_id','proveedor_id');
    }
    public function sucursalesHasProductos()
    {
        return $this->belongsToMany('App\Models\Sucursales','sucursales_has_productos','producto_id','sucursal_id')
        ->withPivot('stock');
    }
}
