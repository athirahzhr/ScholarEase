<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Events\Verified;

use App\Models\UserProfile;
use App\Models\Bookmark;
use App\Models\Application;

class User extends Authenticatable 

{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

public function markEmailAsVerified()
{
    if ($this->hasVerifiedEmail()) {
        return false;
    }

    $this->forceFill([
        'email_verified_at' => $this->freshTimestamp(),
    ])->save();

    event(new Verified($this));

    return true;
}

public function hasVerifiedEmail()
{
    
    if ($this->role === 'admin') {
        return true;
    }

   
    return ! is_null($this->email_verified_at);
}
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
