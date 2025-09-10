<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'ci_usuario',
        'primer_nombre','segundo_nombre',
        'primer_apellido','segundo_apellido',
        'nombre',
        'email','telefono',
        'password','estado_registro','rol',
    ];

    protected $hidden = ['password','remember_token'];

    // Hash automático al asignar 'password' (Laravel 10/11)
    protected $casts = [
        'password' => 'hashed',
    ];
}