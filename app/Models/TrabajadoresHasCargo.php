<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrabajadoresHasCargo extends Model
{
    use HasFactory;
    protected $table = 'trabajadores_has_cargo';
    protected $primaryKey = 'trabajdor_id';
    protected $fillable= ['trabajador_id',
                          'cargo_id'];
    public $timestamps = false;
    public function scopeTrabajadorId($query, $trabajadorId)
    {
        if(!is_null($trabajadorId))
        {
            return $query->where('trabajador_id',$trabajadorId);
        }
        return $query;
    }
    public function scopeCargoId($query, $cargoId)
    {
        if(!is_null($cargoId))
        {
            return $query->where('cargo_id', $cargoId);
        }
        return $query;
    }
    //relaciones
    public function trabajador()
    {
        return $this->belongsTo('App\Models\Trabajadores','trabajador_id');
    }
    public function cargo()
    {
        return $this->belongsTo('App\Models\Cargos','cargo_id');
    }

}
