<?php

namespace App\Http\Controllers;

use App\Actions\CategoryActions;
use App\Helpers\Helpers;
use App\Http\Requests\ArticleDestroyRequest;
use App\Http\Requests\ArticleStoreRequest;
use App\Http\Requests\ArticleUpdateRequest;
use App\Models\Article;
use App\Models\Image;
use App\Models\Paragraph;
use App\Models\Product;
use App\Models\Section;
use App\Models\Subsection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request, CategoryActions $categoryActions)
    {
        $product_id = $request->product_id;
        $category_id = $request->category_id;

        $articles = Article::withPagination()
            ->with([
                'category',
                'sections' => fn($query) => $query->with(['subsections' => fn($query) => $query->with('paragraphs')]),
                'images'
            ]);

        if ($product_id) {
            $product = Product::find($product_id);

            if ($product) {
                $categories_ids = [$product->category_id];
                $categoryActions->allChildrenIds($product->category_id, $categories_ids);

                if (!empty($categories_ids))
                    $articles->whereIn('category_id', $categories_ids);
            }
        } else if ($category_id) {
            $categories_ids = [$category_id];
            $categoryActions->allChildrenIds($category_id, $categories_ids);

            if (!empty($categories_ids))
                $articles->whereIn('category_id', $categories_ids);
        }

        return [
            'articles' => $articles->get()
        ];
    }

    public function store(ArticleStoreRequest $request)
    {
        $data = $request->validated();
        $article = Article::create($data);

        if (isset($data['sections']) && !empty($data['sections'])) {
            $sections = $data['sections'];

            foreach ($sections as $section_data) {
                $section_data['article_id'] = $article->id;
                $section = Section::create($section_data);

                if (isset($section_data['subsections']) && !empty($section_data['subsections'])) {
                    $subsections = $section_data['subsections'];

                    foreach ($subsections as $subsection_data) {
                        $subsection_data['section_id'] = $section->id;
                        $subsection = Subsection::create($subsection_data);

                        if (isset($subsection_data['paragraphs']) && !empty($subsection_data['paragraphs'])) {
                            $paragraphs = $subsection_data['paragraphs'];

                            foreach ($paragraphs as $paragraph_data) {
                                $paragraph_data['subsection_id'] = $subsection->id;
                                $paragraph = Paragraph::create($paragraph_data);
                            }
                        }
                    }
                }

                if (
                    !$article->sections->some(function ($item) use ($section) {
                        return $item->id === $section->id;
                    })
                )
                    $article->sections->push($section);
            }
        }

        if ($request->has('images')) {
            $images = new Collection();

            foreach ($data['images'] as $image_data) {
                $image_data['url'] = Helpers::store_uploaded_file($image_data['url'], ['folder' => 'articles']);
                $image_data['article_id'] = $article->id;
                $image = Image::create($image_data);
                $images->add($image);
            }

            $article->setAttribute('images', $images);
        }

        return [
            'article' => $article
        ];
    }

    public function update(ArticleUpdateRequest $request, Article $article)
    {
        $data = $request->validated();
        $article->update(attributes: $data);

        return [
            'article' => $article
        ];
    }

    public function destroy(ArticleDestroyRequest $request)
    {
        $ids = explode(',', $request->articles_ids);
        
        // FIX: Vérifier les permissions individuellement pour chaque article
        foreach ($ids as $id) {
            $article = Article::find($id);
            if (!$article || !auth()->user()->can('delete', $article)) {
                abort(403, "Vous n'avez pas l'autorisation de supprimer l'article '{$article->title}'");
            }
        }
        
        $deleted = Article::whereIn('id', $ids)->delete();

        return [
            'deleted' => $deleted
        ];
    }
}
