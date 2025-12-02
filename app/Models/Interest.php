<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'allows_documents'
    ];

    protected $casts = [
        'allows_documents' => 'boolean'
    ];

    public function personalInterests()
    {
        return $this->hasMany(PersonalInterest::class);
    }

    public function personalDetails()
    {
        return $this->belongsToMany(PersonalDetail::class, 'personal_interests')
                    ->withTimestamps();
    }
}