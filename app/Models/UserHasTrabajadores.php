<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHasTrabajadores extends Model
{
    use HasFactory;
    protected $table = 'user_has_trabajadores';
    protected $primaryKey ='id';
    protected $fillable = ['id',
                           'user_id',
                           'trabajador_id'];
    public $timestamps = false;

    public function scopeId($query, $id)
    {
        if(!is_null($id))
        {
            return $query->where('id',$id);
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
    public function scopeTrabajadorId($query, $trabajadorId)
    {
        if(!is_null($trabajadorId))
        {
            return $query->where('trabajador_id',$trabajadorId);
        }
    }
    // Relaciones
    public function user()
    {
        return $this->belongsTo(usuarios::class, 'usuario_id','id');
    }
    public function trabajador()
    {
        return $this->belongsTo(Trabajadores::class, 'trabajador_id','id');
    }
}
