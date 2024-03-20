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
        "barrio_id",
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
    ];

    public function barrio()
    {
        return $this->belongsTo(Barrio::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }
}
