<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids; // 1. Added for ULID support
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany; // 2. Added for Relationships
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUlids; // 3. Use HasUlids trait here

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationship: A user can have many posts.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}