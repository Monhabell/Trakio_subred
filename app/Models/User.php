<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
 

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'position_id',
        'subnet_id',
        'environment_id',
        'email',
        'username',
        'password',
        'is_active',
        'role_id',
        'is_admin',
        'terms_accepted'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function notificacionesCumpleaños()
    {
        return $this->morphMany(NotificacionCumpleaños::class, 'notifiable');
    }

    public function subnet()
    {
        return $this->belongsTo(Subnets::class, 'subnet_id', 'id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function entorno()
    {
        return $this->belongsTo(Entorno::class, 'environment_id', 'id');
    }

    public function dataUser()
    {
        return $this->hasOne(DataUser::class, 'id_user', 'id');
    }

    public function consecutivo()
    {
        return $this->hasMany(Consecutivo::class, 'id_user');
    }

    public function userHours()
    {
        return $this->hasMany(UserHour::class);
    }

    public function receptions()
    {
        return $this->belongsToMany(
            Reception::class,
            'reception_user',
            'user_id',
            'reception_id'
        )->withTimestamps();
    }


    /**
     * Relación: Un usuario tiene una calificación del sitio.
     */
    public function siteRating()
    {
        // Asumiendo que crearás el modelo SiteRating en App\Models
        return $this->hasOne(SiteRating::class);
    }

    /**
     * Helper: Verifica si el usuario ya calificó.
     * Retorna true o false.
     */
    public function hasRated()
    {
        return $this->siteRating()->exists();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_admin' => 'boolean',
        ];
    }
}