<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;

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
    ];

    protected $hidden = [
        "token_verificacion",
        "ultimo_envio_email",
        "fecha_verificado",
        "estado_id",
        "barrio_id",
    ];

    public function barrio()
    {
        return $this->belongsTo(Barrio::class);
    }

    public function provincia()
    {
        return $this->belongsTo(Provincia::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }
}
