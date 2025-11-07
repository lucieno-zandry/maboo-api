<?php

namespace App\Http\Controllers\Article;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Article\ImageCreateRequest;
use App\Http\Requests\Article\ImageDeleteRequest;
use App\Models\Image;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function store(ImageCreateRequest $request): array
    {
        $data = $request->validated();
        $images = new Collection();

        foreach ($data['images'] as $image_data) {
            $image_data['url'] = Helpers::store_uploaded_file($image_data['url'], ['folder' => 'articles']);
            $image = Image::create($image_data);
            $images->add($image);
        }

        return [
            'images' => $images,
        ];
    }

    public function destroy(ImageDeleteRequest $request): array
    {
        $ids = explode(',', $request->images_ids);
        
        // FIX: Vérifier les permissions individuellement pour chaque image
        foreach ($ids as $id) {
            $image = Image::find($id);
            if (!$image || !auth()->user()->can('delete', $image)) {
                abort(403, "Vous n'avez pas l'autorisation de supprimer cette image");
            }
        }
        
        $deleted = Image::whereIn('id', $ids)->delete();

        return [
            'deleted' => $deleted
        ];
    }

    public function index(Request $request): array
    {
        $article_id = $request->article_id;

        $images = Image::withPagination();

        if ($article_id)
            $images = $images->where('article_id', $article_id);

        $images = $images->get();

        return [
            'images' => $images
        ];
    }
}
