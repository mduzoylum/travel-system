<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
        ];
    }

    /**
     * Kullanıcının firma ilişkisi
     */
    public function firmUser()
    {
        return $this->hasOne(\App\DDD\Modules\Firm\Models\FirmUser::class);
    }

    /**
     * Kullanıcının rolü admin mi?
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Kullanıcının rolü manager mi?
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Kullanıcının rolü user mi?
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Kullanıcının rolü supplier mi?
     */
    public function isSupplier(): bool
    {
        return $this->role === 'supplier';
    }

    /**
     * Kullanıcının e-postası doğrulanmış mı?
     */
    public function isEmailVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Rol badge rengini al
     */
    public function getRoleBadgeColor(): string
    {
        return match($this->role) {
            'admin' => 'danger',
            'manager' => 'warning',
            'supplier' => 'info',
            'user' => 'success',
            default => 'secondary'
        };
    }

    /**
     * Rol ismini al
     */
    public function getRoleLabel(): string
    {
        return match($this->role) {
            'admin' => 'Admin',
            'manager' => 'Manager',
            'supplier' => 'Tedarikçi',
            'user' => 'Kullanıcı',
            default => 'Bilinmiyor'
        };
    }
}
