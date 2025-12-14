<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function kitchenMasuk()
    {
        return $this->hasMany(KitchenMasuk::class);
    }

    public function kitchenKeluar()
    {
        return $this->hasMany(KitchenKeluar::class);
    }

    public function barMasuk()
    {
        return $this->hasMany(BarMasuk::class);
    }

    public function barKeluar()
    {
        return $this->hasMany(BarKeluar::class);
    }

    public function rotiMasuk()
    {
        return $this->hasMany(RotiMasuk::class);
    }

    public function rotiKeluar()
    {
        return $this->hasMany(RotiKeluar::class);
    }
}
