<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalInterest extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'personal_detail_id',
        'interest_id'
    ];

    public function personalDetail()
    {
        return $this->belongsTo(PersonalDetail::class);
    }

    public function interest()
    {
        return $this->belongsTo(Interest::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}