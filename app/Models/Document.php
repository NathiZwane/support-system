<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'personal_interest_id',
        'file_name',
        'file_path',
        'file_size'
    ];

    public function personalInterest()
    {
        return $this->belongsTo(PersonalInterest::class);
    }
}