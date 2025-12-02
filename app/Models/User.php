<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function ticketsLogged()
    {
        return $this->hasMany(SupportTicket::class, 'logged_by');
    }

    public function ticketActivities()
    {
        return $this->hasMany(TicketActivity::class);
    }

    public function isAdmin()
    {
        return $this->user_type === 'admin';
    }

    public function isSupportAgent()
    {
        return $this->user_type === 'support_agent';
    }

    public function isCustomer()
    {
        return $this->user_type === 'customer';
    }
}
