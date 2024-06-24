<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trabajadores extends Model
{
    use HasFactory;
    protected $table = 'trabajadores';
    protected $primaryKey = 'id';
    protected $fillable = ['id',
                           'admin',
                           'nombre',
                           'activo'];
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
    public function scopeAdmin($query, $admin)
    {
        if(!is_null($admin))
        {
            return $query->where('admin',$admin);
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
    public function scopeSucursalid($query, $sucursalId)
    {
        if(!is_null($sucursalId))
        {
            return $query->where('sucu.id','=',$sucursalId)
                          ->join('sucursales_has_trabajadores AS sutr','sutr.trabajador_id','=','trabajadores.id')
                          ->join('sucursales as sucu','sucu.id','=','sutr.sucursal_id')
                          ->select('trabajadores.id',
                                   'trabajadores.nombre',
                                   'trabajadores.admin',
                                   'trabajadores.activo');
        }
        return $query;
    }
    //Relaciones
    public function sucursalesHasTrabajadores()
    {
        return $this->belongsToMany('App\Models\Sucursales','sucursales_has_trabajadores','trabajador_id','sucursal_id');
    }
    public function cargosHasTrabajadores()
    {
        return $this->belongsToMany('App\Models\Cargos','trabajadores_has_cargo','trabajador_id','cargo_id');
    }
}
