<?php

namespace App\Models;

use App\Actions\CategoryActions;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'parent_id',
        'level',
    ];

    public static function products(int $category_id)
    {
        $relatedCategories = [];
        $relatedCategories[] = $category_id;
        (new CategoryActions)->allChildrenIds($category_id, $relatedCategories);

        return Product::whereIn('category_id', $relatedCategories);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}