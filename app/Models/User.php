<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'gender',
        'phone',
        'location',
        'bio',
        'last_login_at',
        'avatar_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'avatar_path',
    ];

    // Adds avatar_url to every User converted to JSON/array
    protected $appends = ['avatar_url'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /** True when this account is a gym owner (formerly the coach role). */
    public function isGymOwner(): bool
    {
        return $this->role === 'gym_owner';
    }

    /** Gym details for owner accounts (stored in the gym_owners table). */
    public function gymOwner()
    {
        return $this->hasOne(GymOwner::class);
    }

    /** Admins cannot reset their password by email. */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Safety net: admins never get a reset email from any code path
    public function sendPasswordResetNotification($token): void
    {
        if ($this->isAdmin()) {
            return;
        }

        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }

    /** Initials shown in the avatar circle (e.g. "JD"). */
    public function getInitialsAttribute(): string
    {
        return collect(preg_split('/\s+/', trim($this->name)))
            ->filter()->take(2)
            ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
            ->join('') ?: 'A';
    }

    /** First name used in "Welcome back, Admin John!". */
    public function getFirstNameAttribute(): string
    {
        return explode(' ', trim($this->name))[0] ?? $this->name;
    }

    /** Public URL of the profile photo, or null when none is set. */
    public function getAvatarUrlAttribute(): ?string
    {
        return self::avatarUrlFor($this->avatar_path, $this->updated_at);
    }

    /** Use this when you build arrays from DB::table() joins instead of Eloquent. */
    public static function avatarUrlFor(?string $path, $version = null): ?string
    {
        if (!$path) {
            return null;
        }

        $v = $version ? Carbon::parse($version)->timestamp : 0;

        return asset('storage/'.$path).'?v='.$v;
    }
}