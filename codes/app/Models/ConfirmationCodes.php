<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfirmationCodes extends Model
{
    use HasFactory;
    protected $table = 'confirmation_codes';

    protected $fillable = [
        'content',
        'expires_at',
        'user_id'
    ];
    
}
