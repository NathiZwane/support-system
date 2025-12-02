<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketActivity extends Model
{
    use HasFactory;

    public $timestamps = false; 

    protected $fillable = [
        'ticket_id',
        'user_id',
        'activity_type',
        'description'
    ];

    public function ticket()
    {
         return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}