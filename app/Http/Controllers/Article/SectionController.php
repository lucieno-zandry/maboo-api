<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\Article\SectionCreateRequest;
use App\Http\Requests\Article\SectionDeleteRequest;
use App\Http\Requests\Article\SectionUpdateRequest;
use App\Models\Section;
use Illuminate\Http\Request;


class SectionController extends Controller
{
    public function store(SectionCreateRequest $request): array
    {
        $data = $request->validated();
        $section = Section::create($data);

        return [
            'section' => $section
        ];
    }

    public function update(SectionUpdateRequest $request, Section $section)
    {
        $data = $request->validated();

        $section->update($data);

        return [
            'section' => $section
        ];
    }

    public function index(Request $request)
    {
        $article_id = $request->article_id;

        $sections = Section::withPagination();

        if ($article_id)
            $sections = $sections->where('article_id', $article_id);

        $sections = $sections->get();

        return [
            'sections' => $sections
        ];
    }

    public function destroy(SectionDeleteRequest $request)
    {
        $ids = explode(',', $request->sections_ids);
        
        // FIX: Vérifier les permissions individuellement pour chaque section
        foreach ($ids as $id) {
            $section = Section::find($id);
            if (!$section || !auth()->user()->can('delete', $section)) {
                abort(403, "Vous n'avez pas l'autorisation de supprimer cette section");
            }
        }
        
        $deleted = Section::whereIn('id', $ids)->delete();

        return [
            'deleted' => $deleted
        ];
    }
}
