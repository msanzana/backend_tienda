<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedores extends Model
{
    use HasFactory;
    protected $table='proveedores';
    protected $primaryKey = 'id';
    protected $fillable = ['id',
                           'nombre'];
    public $timestamps = false;
    public function scopeId($query, $id)
    {
        if(!is_null($id))
        {
            return $this->where('id',$id);
        }
        return $query;
    } 
    public function scopeNombre($query, $nombre)
    {
        if(!is_null($nombre))
        {    
            return $query->where('nombre', 'LIKE','%'.$nombre.'%');
        }
        return $query;
    }
}
