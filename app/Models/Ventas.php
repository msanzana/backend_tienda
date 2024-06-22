<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ventas extends Model
{
    use HasFactory;
    protected $table = 'ventas';
    protected $primaryKey = 'id';
    protected $fillable = ['id',
                           'fecha',
                           'cliente_id',
                           'sucursal_id',
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
    public function scopeFecha($query, $fecha)
    {
        if(!is_null($fecha))
        {
            return $query->where('fecha',$fecha);
        }
        return $query;
    }
    public function scopeClienteId($query, $clienteId)
    {
        if(!is_null($clienteId))
        {
            return $query->where('cliente_id', $clienteId);
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
    public function scopeTotal($query, $total)
    {
        if(!is_null($total))
        {
            return $query->where('total', $total);
        }
        return $query;
    }
    // relaciones
    public function cliente()
    {
        return $this->belongsTo('App\Models\Clientes', 'cliente_id');
    }
    public function sucursal()
    {
        return $this->belongsTo('App\Models\Sucursales', 'sucursal_id');
    }
    public function ventaDetalle()
    {
        return $this->hasMany('App\Models\VentasDetalle','venta_id','id');
    }

}
