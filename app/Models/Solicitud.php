<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solicitud extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'solicitudes';

    protected $fillable = [
        "lastname",
        "name",
        "cuit",
        "email",
        "phone",

        /* Cuando no es de Cutral Co el valor es null */
        "barrio_id",

        /* Cuando selecciono otra localidad, barrio_id deberia ser null */
        'provincia_id',
        'municipio',
        'barrio',

        "calle",
        "altura",
        "manzana",
        "lote",
        "piso",
        "depto",

        "token_verificacion",
        "ultimo_envio_email",
        "fecha_verificado",
        "estado_id",
        "is_afd",
    ];

    protected $hidden = [
        "token_verificacion",
        "ultimo_envio_email",
        "fecha_verificado",
        "estado_id",
        "barrio_id",
    ];

    protected $casts = [
        'is_afd' => 'boolean',
    ];

    public function barrio_municipio()
    {
        return $this->belongsTo(BarrioMunicipio::class, 'barrio_id');
    }

    public function provincia()
    {
        return $this->belongsTo(Provincia::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public static function getCountSinVerificar()
    {
        return self::whereNull('fecha_verificado')->count();
    }

    public static function getCountPendientes()
    {
        return self::whereNotNull('fecha_verificado')->where('estado_id', 1)->count();
    }

    public static function getCountAprobadas()
    {
        return self::where('estado_id', 2)->count();
    }
}
