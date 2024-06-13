<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHasClientes extends Model
{
    use HasFactory;
    protected $table = 'user_has_clientes';
    protected $primaryKey = 'id';
    protected $fillable = ['id',
                           'user_id',
                           'cliente_id'];
    public $timestamps = false;
    public function scopeId($query, $id)
    {
        if(!is_null($id))
        {
            return $query->where('id','=',$id);
        }
        return $query;
    }
    public function scopeUserId($query, $userId)
    {
        if(!is_null($userId))
        {
            return $query->where('user_id',$userId);
        }
        return $query;
    }
    public function scopeClienteId($query, $clienteId)
    {
        if(!is_null($clienteId))
        {
            return $query->where('cliente_id',$clienteId);
        }
        return $query;
    }
    // Relaciones
    public function user()
    {
        return $this->hasOne(Usuarios::class,'user_id','id');
    }
    public function cliente()
    {
        return $this->hasOne(Clientes::class,'cliente_id','id');
    }

}
