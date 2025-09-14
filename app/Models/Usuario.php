<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    // Nombre de la tabla en la BD
    protected $table = 'usuarios';

    // Clave primaria (CI en formato string)
    protected $primaryKey = 'ci_usuario';
    public $incrementing = false;
    protected $keyType = 'string';

    // Campos que se pueden asignar en masa
    protected $fillable = [
        'ci_usuario',
        'email',
        'password',
        'estado_registro',
        'rol',
    ];

    // Campos ocultos en JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Normaliza la CI antes de guardar (solo dígitos, máx 8).
     */
    public function setCiUsuarioAttribute($value)
    {
        $this->attributes['ci_usuario'] = substr(preg_replace('/\D/', '', (string) $value), 0, 8);
    }
}