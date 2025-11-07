<?php

namespace App\Models;

use App\Traits\WithPagination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory, WithPagination;

    /**
     * Les attributs qui peuvent être assignés en masse.
     * CORRECTION: Ajout des champs fillable manquants pour permettre
     * la création d'images d'articles via le CRUD frontend.
     */
    protected $fillable = [
        'url',
        'caption',
        'order',
        'article_id'
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
