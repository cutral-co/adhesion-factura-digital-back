<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    protected $connection = 'admin';

    protected $table = 'provincias';

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'provincia_id');
    }
}
