<?php

namespace App\Models;

use App\Traits\WithPagination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subsection extends Model
{
    use HasFactory, WithPagination;

    protected $fillable = [
        'title',
        'order',
        'section_id'
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function paragraphs()
    {
        return $this->hasMany(Paragraph::class);
    }
}
