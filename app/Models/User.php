<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        "name",
        "email",
        "username",
        "password",
        "must_change_pw",
        "avatar",
        "fcm_token",
        "is_active",
    ];

    protected $hidden = ["password", "remember_token"];

    protected function casts(): array
    {
        return [
            "password" => "hashed",
            "must_change_pw" => "boolean",
            "is_active" => "boolean",
        ];
    }

    public function wali(): HasOne
    {
        return $this->hasOne(Wali::class);
    }

    public function ustadz(): HasOne
    {
        return $this->hasOne(Ustadz::class);
    }

    public function outlet(): HasOne
    {
        return $this->hasOne(Outlet::class);
    }
}
