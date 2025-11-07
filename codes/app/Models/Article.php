<?php

namespace App\Models;

use App\Traits\WithPagination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory, WithPagination;

    protected $fillable = [
        'title',
        'author',
        'category_id',
        'user_id'
    ];

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
