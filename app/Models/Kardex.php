<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    use HasFactory;
    protected $table = 'kardex';
    protected $primaryKey = 'id';
    protected $fillable = ['id',
                           'fecha',
                           'modulo_id',
                           'correlativo',
                           'producto_id',
                           'sucursal_id',
                           'cantidad_actual',
                           'nueva_cantidad'];
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
    public function scopeModuloId($query, $moduloId)
    {
        if(!is_null($moduloId))
        {
            return $query->where($query, $moduloId);
        }
        return $query;
    }
    public function scopeCorrelativo($query, $correlativo)
    {
        if(!is_null($correlativo))
        {
            return $query->where('correlativo', $correlativo);
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
    public function scopeSucursalId($query, $sucursalId)
    {
        if(!is_null($sucursalId))
        {
            return $query->where('sucursal_id', $sucursalId);
        }
        return $query;
    }
    // relaciones
    public function modulo()
    {
        return $this->belongsTo('App\Models\Modulos','modulo_id');
    }
    public function producto()
    {
        return $this->belongsTo('App\Models\Productos','producto_id');
    }
    public function sucursal()
    {
        return $this->belongsTo('App\Models\Sucursales','sucursal_id');
    }
}
