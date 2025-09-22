<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable; // <-- TAMBAHKAN BARIS INI

class User extends Authenticatable
{
    use HasFactory, Notifiable; // <-- trait tetap dipakai

    protected $fillable = ['username', 'password', 'role'];
    protected $hidden   = ['password', 'remember_token'];

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isUser(): bool  { return $this->role === 'user'; }
}
