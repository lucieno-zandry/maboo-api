<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paragraph extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'subsection_id'
    ];
    
    public function subsection()
    {
        return $this->belongsTo(Subsection::class);
    }
}
