<?php

namespace App\Models;

use App\Traits\WithPagination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessionalData extends Model
{
    use HasFactory, WithPagination;

    protected $fillable = [
        'title',
        'specialization',
        'experience',
        'rating',
        'description',
        'services',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
