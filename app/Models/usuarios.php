<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class usuarios extends Model
{
    use HasFactory;
    protected $table = 'users';
    protected $primaryKey ='id';
    protected $fillable =['id',
                          'name',
                          'email',
                          'email_verified_at',
                          'password',
                          'remember_token'];
    public $timestamps = true;
    public function scopeId($query,$id)
    {
        if(!is_null($id))
        {
            return $query->where('id',$id);
        }
        return $query;
    }
    public function scopeName($query, $name)
    {
        if(!is_null($name))
        {
            return $query->where('name','LIKE','%'.$name.'%');
        }
        return $query;
    }
    //relaciones
    public function userHasClientes()
    {
        return $this->hasOne('App\Models\UserHasClientes','user_id','id');
    }
    public function userHasTrabajadores()
    {
        return $this->hasOne('App\Models\UserHasTrabajadores','user_id','id');
    }
    
}
