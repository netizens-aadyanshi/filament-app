<?php

namespace App\Models;

use App\Role;
use App\Status;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthenticationRecovery;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Support\Facades\Storage;


class User extends Authenticatable implements MustVerifyEmail,HasAvatar, FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, InteractsWithAppAuthentication, InteractsWithAppAuthenticationRecovery;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'role',
        'interests',
        'status',
        'profile_photo',
        'bio',
        'notes',
        'address',
        'ban_reason',
        'preferences',
        'label_color',
        'trust_score1',

    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

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
            'address' => 'array',
            'interests' => 'array',
            'preferences' => 'array',
            'role' => Role::class,
            'status' => Status::class,

        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // For development, return true to allow all users.
        // In production, you might use: return $this->role === Role::Admin;
        return true;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->profile_photo
            ? Storage::url($this->profile_photo)  // Full storage URL
            : null;
    }
}
