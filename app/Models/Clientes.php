<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    use HasFactory;
    protected $table = 'cliente';
    protected $primaryKey = 'id';
    protected $fillable =['id',
                          'nombre'];
    public $timestamps = false;
    public function scopeId($query, $id)
    {
        if(!is_null($id))
        {
            return $query->where('id',$id);
        }
        return $query;
    }
    public function scopeNombre($query,$nombre)
    {
        if(!is_null($nombre))
        {
            return $query->where('nombre','LIKE','%'.$nombre.'%');
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
}
