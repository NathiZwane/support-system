<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'category',
        'first_name',
        'last_name',
        'email',
        'phone',
        'company',
        'subject',
        'description',
        'priority',
        'status',
        'latitude',
        'longitude',
        'logged_by'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Security: Prevent mass assignment vulnerability
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loggedBy()
    {
        return $this->belongsTo(User::class, 'logged_by');
    }

   public function activities()
    {
        return $this->hasMany(TicketActivity::class, 'ticket_id');
    }

    // Generate unique ticket number
    public static function generateTicketNumber()
    {
        do {
            $number = 'TKT' . date('Ymd') . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('ticket_number', $number)->exists());

        return $number;
    }

    // Security: XSS prevention for description field
    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function getDescriptionAttribute($value)
    {
        return htmlspecialchars_decode($value, ENT_QUOTES);
    }
}