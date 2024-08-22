<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
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
        'google2fa_secret',
        'google2fa_enabled',
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
    ];


    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->BelongsToMany(Role::class);
    }

    public function restrictedEvents(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'restricted_event_users', 'user_id', 'event_id');
    }

    public function eventsAdmin(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_admin_users', 'user_id', 'event_id');
    }

    public function role()
    {
        $user = $this;
        $actualRole = session('role');
        //Check if is still valid
        $userRoles = $user->roles;

        foreach ($userRoles as $role) {
            if ($actualRole === $role->id) {
                return $role;
            }
        }
    }

    public function hasRole(string $roleName): bool
    {
        try {
            $roleNumber = Role::getRoleNumber($roleName);
        } catch (\RuntimeException $e) {
            return false;
        }
        return $this->role()->customId >= $roleNumber;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('administrador');
    }

}
