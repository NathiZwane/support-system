<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'date_of_birth'
    ];

    protected $casts = [
        'date_of_birth' => 'date'
    ];

    public function personalInterests()
    {
        return $this->hasMany(PersonalInterest::class);
    }

    public function interests()
    {
        return $this->belongsToMany(Interest::class, 'personal_interests')
                    ->withTimestamps();
    }
}