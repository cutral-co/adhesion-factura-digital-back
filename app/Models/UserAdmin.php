<?php

namespace App\Models;

class UserAdmin extends \Illuminate\Database\Eloquent\Model
{
    protected $connection = 'admin';

    protected $table = 'users';

    protected $fillable = [
        'id',
        'cuit',
        'password',
        'person_id',
        'is_verified'
    ];

    protected $hidden = [
        'person_id',
        'password',
        'created_at',
        'updated_at',
        'permissions',
        'roles'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
